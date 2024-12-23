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
        return 'Custom Tools';
    }

    public function mount()
    {
        $this->locations = Location::with('property')->get()->map(function ($location) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$location->longitude, $location->latitude]
                ],
                'properties' => [
                    'id' => $location->property->id,
                    'name' => $location->property->name,
                    'address' => $location->property->address,
                    'price' => $location->property->price,
                    'location_name' => $location->location_name,
                ]
            ];
        })->values()->toArray();
    }
}