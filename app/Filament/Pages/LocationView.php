<?php

namespace App\Filament\Pages;

use App\Models\Location;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LocationView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static string $view = 'filament.pages.location-view';
    protected static ?string $navigationLabel = 'Boarding House Locations';
    protected static ?string $title = 'Boarding House Locations';
    
    public $locations = [];

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Locations';
    }

    public function mount()
    {
        // Fetch locations and format them for the map
        $this->locations = Location::with('property')->get()->map(function ($location) {
            return [
                'longitude' => $location->longitude,
                'latitude' => $location->latitude,
                'property' => [
                    'name' => $location->property->name,
                    'price' => $location->property->price,
                    'status' => $location->property->availability_status,
                    'room_type' => $location->property->room_type,
                ]
            ];
        })->toArray();
    }

    // You can also create a helper method to get the locations if needed
    public function getLocations()
    {
        return $this->locations;
    }
}
