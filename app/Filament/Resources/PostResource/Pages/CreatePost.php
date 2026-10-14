<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Concerns\SavesTranslatedForms;
use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    use SavesTranslatedForms;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return $this->createTranslatedRecord($data);
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();
        $this->swapTranslatedLocaleData();
    }
}
