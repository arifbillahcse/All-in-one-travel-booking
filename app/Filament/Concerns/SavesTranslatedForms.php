<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * Makes the language switcher (spatie translatable plugin) safe for fields that change shape
 * between the database and the form: repeaters, "simple" repeaters and the article block builder.
 *
 * The plugin keeps the other languages in the database format and puts it straight back into the
 * form, which breaks those fields. Here the language being left is converted to the database format
 * before it is parked, and the language being opened is hydrated like a normal form fill. On save,
 * every parked language is hydrated, validated and dehydrated before it is written.
 *
 * Pages call these helpers from small overrides of the plugin's methods.
 */
trait SavesTranslatedForms
{
    /** Park the language we leave (database format) and open the one we switch to. */
    protected function swapTranslatedLocaleData(): void
    {
        $attributes = static::getResource()::getTranslatableAttributes();

        // Dehydrate without validating, so an unfinished language never blocks the switch.
        $full = [];
        Arr::set($full, $this->form->getStatePath(), $this->form->getRawState());
        $this->form->dehydrateState($full);
        $this->form->mutateDehydratedState($full);
        $stored = data_get($full, $this->form->getStatePath()) ?? [];

        $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($stored, $attributes);

        $incoming = $this->otherLocaleData[$this->activeLocale] ?? [];
        unset($this->otherLocaleData[$this->activeLocale]);

        $this->form->fill([...Arr::except($this->data, $attributes), ...$incoming]);
    }

    /**
     * Run each parked language through the form (hydrate -> validate -> dehydrate).
     *
     * @return array<string, array<string, mixed>> locale => translated values in database format
     */
    protected function processParkedLocales(): array
    {
        $attributes = static::getResource()::getTranslatableAttributes();
        $original = $this->data;
        $processed = [];

        foreach ($this->otherLocaleData as $locale => $localeData) {
            $this->form->fill([...Arr::except($original, $attributes), ...$localeData]);

            try {
                $processed[$locale] = Arr::only($this->form->getState(), $attributes);
            } catch (ValidationException $exception) {
                $this->data = $original;
                $this->setActiveLocale($locale);   // show the language that needs attention

                throw $exception;
            }
        }

        $this->data = $original;

        return $processed;
    }

    protected function updateTranslatedRecord(Model $record, array $data): Model
    {
        $attributes = static::getResource()::getTranslatableAttributes();

        $record->fill(Arr::except($data, $attributes));

        foreach (Arr::only($data, $attributes) as $key => $value) {
            $record->setTranslation($key, $this->activeLocale, $value);
        }

        foreach ($this->processParkedLocales() as $locale => $values) {
            foreach ($values as $key => $value) {
                $record->setTranslation($key, $locale, $value);
            }
        }

        $record->save();

        return $record;
    }

    protected function createTranslatedRecord(array $data): Model
    {
        $record = app(static::getModel());
        $attributes = static::getResource()::getTranslatableAttributes();

        $record->fill(Arr::except($data, $attributes));

        foreach (Arr::only($data, $attributes) as $key => $value) {
            $record->setTranslation($key, $this->activeLocale, $value);
        }

        foreach ($this->processParkedLocales() as $locale => $values) {
            foreach ($values as $key => $value) {
                $record->setTranslation($key, $locale, $value);
            }
        }

        $record->save();

        return $record;
    }
}
