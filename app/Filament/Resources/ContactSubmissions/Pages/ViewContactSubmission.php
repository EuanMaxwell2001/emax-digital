<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record->read_at) {
            $this->record->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->url(fn () => 'mailto:'.$this->record->email.'?subject='.rawurlencode('Re: your message to EMAX Digital')),
            DeleteAction::make(),
        ];
    }
}
