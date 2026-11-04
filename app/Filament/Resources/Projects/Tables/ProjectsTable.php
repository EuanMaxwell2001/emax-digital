<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->recordUrl(fn (Project $p) => \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $p]))
            ->description('The order here is the order on the homepage. Use the reorder button to drag them.')
            ->columns([
                ImageColumn::make('cover_path')->label('')->disk('public')->imageHeight(48),
                TextColumn::make('title')->searchable()->weight('bold')
                    ->description(fn (Project $p) => $p->sector),
                TextColumn::make('services')->badge()->separator(','),
                TextColumn::make('year')->placeholder('–'),
                ToggleColumn::make('is_published')->label('Published'),
                TextColumn::make('updated_at')->label('Updated')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('View on site')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Project $p) => $p->url())
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
