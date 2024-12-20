<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-horizontal-circle';

    protected static ?string $navigationGroup = 'Reservation Management';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('user_id')
                //     ->label('User ID')
                //     ->disabled()
                //     ->required(),

                Forms\Components\Select::make('user_id')
                    ->options(function () {
                        return \App\Models\User::where('role', 'user')
                            ->get()
                            ->mapWithKeys(function ($reservation) {
                                return [
                                    $reservation->id => $reservation->name,
                                ];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->label('User')
                    ->disabled()
                    ->required(),

                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->disabled()
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Message')
                    ->disabled()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('date_reserved')
                    ->label('Date Reserved')
                    ->disabled()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending'       => 'pending',
                        'reserved'      => 'reserved'
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('property.name')
                    ->label('Property')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Message')
                    ->limit(50),
                TextColumn::make('date_reserved')
                    ->label('Date Reserved')
                    ->date(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Reservation Status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reserved' => 'success',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return false;
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('property', function (Builder $query) {
                // Ensure the property belongs to the authenticated user
                $query->where('user_id', Auth::id());
            });
    }
}
