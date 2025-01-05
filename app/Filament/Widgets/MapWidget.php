<?php

namespace App\Filament\Widgets;

use App\Models\Location;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MapWidget extends Widget 
{
    protected static string $view = 'filament.widgets.map-widget';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::user()->role === 'owner' && Auth::user()->isActive === 'active';
    }

    public function getLocations(): array
    {
        return Location::with(['property' => function ($query) {
            $query->where('user_id', Auth::id());
        }])->get()->map(function ($location) {
            if ($location->property) { 
                return [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'name' => $location->location_name,
                    'property' => [
                        'name' => $location->property->name,
                        'price' => $location->property->price,
                        'status' => $location->property->availability_status,
                        'room_type' => $location->property->room_type,
                    ]
                ];
            }
            return null;
        })->filter()->values()->toArray(); // Remove null values and reindex
    }
}
