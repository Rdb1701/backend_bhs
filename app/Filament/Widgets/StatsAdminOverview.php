<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsAdminOverview extends BaseWidget
{
    protected static bool $isLazy = false;


    public static function canView(): bool
    {
        return Auth::user()->role === 'owner' && Auth::user()->isActive === 'active';
    }

    protected function getStats(): array

    {
        $userId = Auth::id();
        if (Auth::user()->role === "owner") {


            return [

                Stat::make('Boarding Houses', Property::where('user_id', Auth::id())->count())
                    ->description('Total number of Boarding Houses')
                    ->color('danger')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->icon('heroicon-o-rectangle-stack'),

                Stat::make('Reservations', Reservation::whereHas('property', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->where('status', 'pending')->count())
                    ->description('Total Number of Pending Reservations')
                    ->color('warning')
                    ->chart([3, 5, 8, 10, 6, 7, 9])
                    ->icon('heroicon-o-calendar'),

                Stat::make('Reservations', Reservation::whereHas('property', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->where('status', 'reserved')->count())
                    ->description('Total Number of Pending Reservations')
                    ->color('success')
                    ->chart([3, 5, 8, 10, 6, 7, 9])
                    ->icon('heroicon-o-calendar'),
            ];
        }

        if (Auth::user()->role === "admin") {
            return [

                Stat::make('Boarding Houses', Property::all()->count())
                    ->description('Total number of Boarding Houses')
                    ->color('danger')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->icon('heroicon-o-rectangle-stack'),

                Stat::make('Reservations', Reservation::where('status', 'pending')->count())
                    ->description('Total Number of Pending Reservations')
                    ->color('warning')
                    ->chart([3, 5, 8, 10, 6, 7, 9])
                    ->icon('heroicon-o-calendar'),

                Stat::make('Reservations', Reservation::where('status', 'reserved')->count())
                    ->description('Total Number of Pending Reservations')
                    ->color('success')
                    ->chart([3, 5, 8, 10, 6, 7, 9])
                    ->icon('heroicon-o-calendar'),
            ];
        }

    }
}
