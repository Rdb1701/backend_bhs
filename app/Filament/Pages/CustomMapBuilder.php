<?php

namespace App\Filament\Pages;

use App\Models\Property;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CustomMapBuilder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'filament.pages.custom-map-builder';
    protected static ?string $navigationLabel = 'Map Builder';
    protected static ?string $title = 'Custom Map Builder';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'owner';
    }

    public $properties = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Custom Tools';
    }

    public function mount()
    {
   
        $this->properties = Property::where('user_id', Auth::id())->get();
    }
}
