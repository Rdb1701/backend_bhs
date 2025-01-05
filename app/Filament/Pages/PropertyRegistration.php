<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\PayMongoService;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PropertyRegistration extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Registration';
    protected static ?string $title = 'Registration Payment';
    protected static string $view = 'filament.pages.property-registration';
    
       public static function canAccess(): bool
    {
        return Auth::user()->role === 'owner';
    }

    protected function getHeaderActions(): array
    {
        if (Auth::user()->isActive === 'active') {
            return [];
        }

        return [
            Action::make('pay')
                ->label('Pay ₱300')
                ->button()
                ->action(function () {
                    try {
                        $paymongoService = new PayMongoService(config('services.paymongo.secret_key'));
                        
                        $session = $paymongoService->createCheckoutSession([
                            'amount' => 300 * 100, // Amount in cents
                            'currency' => 'PHP',
                            'description' => 'Property Registration Payment for ' . Auth::user()->email
                        ]);

                        $user = Auth::user();

                        Log::info('Updating user with payment intent', ['user_id' => $user->id, 'payment_intent_id' => $session->id]);

                        $user->update([
                            'payment_intent_id' => $session->id,
                            'payment_status' => 'pending',
                        ]);

                        return redirect()->to($session->checkout_url);
                    } catch (\Exception $e) {
                        Log::error('Payment error', ['error' => $e->getMessage()]);
                        
                        Notification::make()
                            ->title('Payment Error')
                            ->body('Unable to process payment. Please try again.')
                            ->danger()
                            ->send();
                    }
                })
        ];
    }
}

