<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Property Management';

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'owner';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
                Forms\Components\TextInput::make('address')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required(),
                Forms\Components\TextInput::make('persons_per_room')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('contact_number')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('availability_status')
                    ->options([
                        'Available'     => 'available',
                        'Not available' => 'not available',
                    ])
                    ->native(false)
                    ->label('Availability')
                    ->required(),
                Forms\Components\Select::make('room_type')
                    ->options([
                        'studio'    => 'studio',
                        'pad'       => 'pad',
                        'apartment' => 'apartment',
                        'room only' => 'room only',
                    ])
                    ->native(false)
                    ->label('Room Type')
                    ->required(),

                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'heading',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'link',
                        'codeBlock'
                    ])
                    ->placeholder('Write a detailed description with formatting...'),
                Forms\Components\FileUpload::make('photos')
                    ->label('Room Photos')
                    ->required()
                    ->imageResizeMode('cover')
                    ->openable()
                    ->multiple()
                    ->downloadable()
                    ->panelLayout('grid')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('amenities')
                    ->label('Amenities')
                    ->schema([
                        Forms\Components\TextInput::make('amenity')
                            ->label('Amenity')
                            ->required(),
                    ])
                    ->createItemButtonLabel('Add Amenity')
                    ->minItems(1)
                    ->required()
                    ->columnSpanFull(),
                // Forms\Components\View::make('filament.components.mapbox')
                //     ->label('Map')
                //     ->reactive()
                //     ->afterStateUpdated(function ($state, callable $set) {
                //         // Automatically update the lat and long inputs when the map is clicked
                //         if (isset($state['lat']) && isset($state['long'])) {
                //             $set('lat', $state['lat']);
                //             $set('long', $state['long']);
                //         }
                //     }),

                // Forms\Components\TextInput::make('lat')
                //     ->label('Latitude')
                //     ->placeholder("Please copy the latitude above")
                //     ->required()
                //     ->numeric()
                //     ->afterStateUpdated(function ($state, callable $set) {
                //         // Update the map's state with the entered latitude if needed
                //         $set('lat', $state);
                //     }),

                // Forms\Components\TextInput::make('long')
                //     ->label('Longitude')
                //     ->required()
                //     ->placeholder("Please copy the longitude above")
                //     ->numeric()
                //     ->afterStateUpdated(function ($state, callable $set) {
                //         // Update the map's state with the entered longitude if needed
                //         $set('long', $state);
                //     }),


            ])->columns(1);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable()
                    ->formatStateUsing(fn($state) => '₱' . $state),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('persons_per_room')
                    ->searchable(),
                Tables\Columns\TextColumn::make('availability_status')
                    ->label("Availability")
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state): string => match ($state) {
                        'Available'     => 'success',
                        'Not available' => 'danger',
                    }),
                Tables\Columns\ImageColumn::make('photos')
                    ->stacked()
                    ->circular()
                    ->limit(3)
                    ->limitedRemainingText(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
