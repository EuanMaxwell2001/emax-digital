<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use App\Models\ContactSubmission;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->copyable()
                            ->url(fn (ContactSubmission $r) => 'mailto:'.$r->email),
                        TextEntry::make('created_at')->label('Sent')->dateTime('j M Y, H:i'),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->extraAttributes(['style' => 'white-space: pre-line']),
                        TextEntry::make('page')->label('Sent from')->placeholder('–'),
                        TextEntry::make('ip')->label('IP address')->placeholder('–'),
                        TextEntry::make('user_agent')->label('Browser')->placeholder('–')->limit(80),
                    ]),
            ]);
    }
}
