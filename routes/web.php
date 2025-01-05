<?php

use App\Http\Controllers\LocationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin'); 
});


Route::middleware('auth')->post('/save-location', [LocationController::class, 'saveLocation'])->name('save-location');
Route::middleware('auth')->get('/map-selection', [LocationController::class, 'showMapPage'])->name('map-selection');


Route::middleware('auth')->get('/locations_view', [LocationController::class, 'showLocationPage'])->name('location-pages');


Route::post('webhooks/paymongo', function (Request $request) {
    Log::info('PayMongo webhook received', $request->all());

    try {
        $payload = $request->all();
        
        if ($payload['data']['attributes']['type'] === 'checkout_session.payment.paid') {
            Log::info('Checkout session completed', ['session_id' => $payload['data']['attributes']['data']['id']]);
            
            $sessionId = $payload['data']['attributes']['data']['id'];
            $user = User::where('payment_intent_id', $sessionId)->first();
            
            if ($user) {
                Log::info('User found', ['user_id' => $user->id]);
                
                $user->update([
                    'isActive' => 'active',
                    'payment_status' => 'paid'
                ]);
                
                Log::info('User updated', ['user_id' => $user->id, 'isActive' => $user->isActive, 'payment_status' => $user->payment_status]);
            } else {
                Log::warning('User not found for session', ['session_id' => $sessionId]);
            }
        } else {
            Log::info('Non-completed checkout session event', ['type' => $payload['data']['attributes']['type']]);
        }
    } catch (\Exception $e) {
        Log::error('Error processing PayMongo webhook', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Internal server error'], 500);
    }

    return response()->json(['success' => true]);
})->name('paymongo.webhook');


