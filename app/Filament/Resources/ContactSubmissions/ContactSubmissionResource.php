<?php

namespace App\Filament\Resources\ContactSubmissions;

use App\Filament\Resources\ContactSubmissions\Pages\ListContactSubmissions;
use App\Filament\Resources\ContactSubmissions\Pages\ViewContactSubmission;
use App\Filament\Resources\ContactSubmissions\Schemas\ContactSubmissionInfolist;
use App\Filament\Resources\ContactSubmissions\Tables\ContactSubmissionsTable;
use App\Models\ContactSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/** Messages from the contact form. Read-only: they can be read and deleted, not edited. */
class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'message';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactSubmission::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactSubmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactSubmissionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactSubmissions::route('/'),
            'view' => ViewContactSubmission::route('/{record}'),
        ];
    }
}
