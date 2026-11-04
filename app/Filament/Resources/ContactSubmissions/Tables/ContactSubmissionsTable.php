<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use App\Models\ContactSubmission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (ContactSubmission $r) => \App\Filament\Resources\ContactSubmissions\ContactSubmissionResource::getUrl('view', ['record' => $r]))
            ->columns([
                TextColumn::make('name')->searchable()
                    ->weight(fn (ContactSubmission $r) => $r->read_at ? null : 'bold')
                    ->description(fn (ContactSubmission $r) => $r->email),
                TextColumn::make('message')->limit(80)->wrap()->searchable(),
                TextColumn::make('created_at')->label('Sent')->since()->sortable()
                    ->dateTimeTooltip('j M Y, H:i'),
            ])
            ->filters([
                TernaryFilter::make('unread')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNull('read_at'),
                        false: fn (Builder $q) => $q->whereNotNull('read_at'),
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No messages yet')
            ->emptyStateDescription('Messages from the contact form on the site appear here.');
    }
}
