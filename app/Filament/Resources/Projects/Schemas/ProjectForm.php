<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\ProjectDetail;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    /** Uploads: the site makes AVIF/WebP sizes from these after saving. */
    private static function image(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory('projects')
            ->visibility('public')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(12 * 1024)
            ->imagePreviewHeight('180')
            ->required();
    }

    private static function chars(?string $state, int $min, int $max): string
    {
        $n = Str::length((string) $state);

        return $n.' characters'.($n < $min || $n > $max ? " (aim for {$min} to {$max})" : '');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Project')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                if ($operation === 'create' || blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->prefix('/work/')
                            ->helperText('The page address. Changing it breaks old links to this project.'),
                        TextInput::make('sector')
                            ->label('Client / sector')
                            ->required()
                            ->maxLength(120)
                            ->placeholder('Edinburgh printers'),
                        TagsInput::make('services')
                            ->label('What EMAX did')
                            ->required()
                            ->suggestions(['Website', 'Logo', 'Branding', 'Hosting', 'App'])
                            ->placeholder('Add a service'),
                        TextInput::make('year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->helperText('Leave empty to hide it.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Hidden projects can still be previewed while you are signed in.')
                            ->inline(false),
                    ]),

                Section::make('Summary and search')
                    ->schema([
                        Textarea::make('summary')
                            ->required()
                            ->rows(3)
                            ->maxLength(400)
                            ->helperText('One or two sentences, shown on the homepage card and under the title.'),
                        Textarea::make('description')
                            ->label('Search description')
                            ->required()
                            ->rows(2)
                            ->maxLength(300)
                            ->live(debounce: 400)
                            ->hint(fn (?string $state) => self::chars($state, 120, 160))
                            ->helperText('What Google shows under the link.'),
                    ]),

                Section::make('Cover image')
                    ->description('The homepage card and the big image at the top of the case study. 1600 × 1000 works best.')
                    ->columns(2)
                    ->schema([
                        self::image('cover_path')->label('Image'),
                        Textarea::make('cover_alt')
                            ->label('Alt text')
                            ->required()
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('Describe what the image shows, for people who cannot see it.'),
                    ]),

                Section::make('Case study')
                    ->schema([
                        Textarea::make('context')->label('The brief')->required()->rows(4),
                        Textarea::make('idea')->label('The idea')->required()->rows(4),
                        Textarea::make('outcome')->label('The result')->required()->rows(3),
                    ]),

                Section::make('Up close')
                    ->description('Close-up images shown between the idea and the result. Drag to reorder.')
                    ->schema([
                        Repeater::make('details')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Add an image')
                            ->itemLabel(fn (array $state) => $state['caption'] ?? null ?: 'Image')
                            ->columns(2)
                            ->schema([
                                self::image('image_path')->label('Image'),
                                Select::make('shape')
                                    ->options(ProjectDetail::SHAPES)
                                    ->default('wide')
                                    ->required()
                                    ->selectablePlaceholder(false)
                                    ->helperText('How large it sits on the page.'),
                                Textarea::make('alt')->label('Alt text')->required()->rows(2)->maxLength(250),
                                TextInput::make('caption')->maxLength(250),
                            ]),
                    ]),
            ]);
    }
}
