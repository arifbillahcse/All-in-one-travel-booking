<?php

namespace App\Filament\Resources\AddonResource\Pages;

use App\Filament\Concerns\SavesTranslatedForms;
use App\Filament\Resources\AddonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAddon extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    use SavesTranslatedForms;

    protected static string $resource = AddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->updateTranslatedRecord($record, $data);
    }

    public function updatedActiveLocale(): void
    {
        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();
        $this->swapTranslatedLocaleData();
    }
}
