<?php

use App\Http\Controllers\ReviewController;
use App\Models\Location;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as FacadesPassword;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    });

    // Properties routes within authenticated group
    Route::get('/properties', function () {
        $properties = Property::with('user')
            ->latest()
            ->get()
            ->map(function ($property) {
                // photos and amenities arrays
                $property->photos = $property->photos ?? [];
                $property->amenities = $property->amenities ?? [];
                return $property;
            });

        return response()->json([
            'properties' => $properties,
            'total' => $properties->count()
        ]);
    });

    Route::get('/properties/{id}', function ($id) {
        $property = Property::with('user')
            ->findOrFail($id);

        $property->photos = $property->photos ?? [];
        $property->amenities = $property->amenities ?? [];
        $property->location = [
            'lat' => $property->lat,
            'long' => $property->long,
        ];

        return response()->json($property);
    });

    // MAP
    Route::get('/properties_map/{id}', function ($id) {
        $property = Property::with('user')->findOrFail($id);

        // Get the location details from the 'locations' table
        $location = Location::where('property_id', $id)->first();


        if ($location) {
            $property->location = [
                'lat' => $location->latitude,
                'long' => $location->longitude,
                'location_name' => $location->location_name,
            ];
        } else {
            // If no location is found, return a default empty location
            $property->location = [
                'lat' => null,
                'long' => null,
                'location_name' => null,
            ];
        }

        // Return the property with the location information
        return response()->json($property);
    });



    //RESERVATIONS

    // Create reservation
    Route::post('/reservations', function (Request $request) {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'description' => 'required|string',
            'date_reserved' => 'required|date|after_or_equal:today',
        ]);

        $reservation = new Reservation([
            'user_id' => $request->user()->id,
            'property_id' => $request->property_id,
            'description' => $request->description,
            'date_reserved' => $request->date_reserved,
            'status' => 'pending'
        ]);

        $reservation->save();

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation
        ], 201);
    });

    // Get user's reservations
    Route::get('/reservations', function (Request $request) {
        $reservations = Reservation::with(['property', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'reservations' => $reservations
        ]);
    });

    // Delete reservation
    Route::delete('/reservations/{id}', function (Request $request, $id) {
        $reservation = Reservation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $reservation->delete();

        return response()->json([
            'message' => 'Reservation cancelled successfully'
        ]);
    });

    //PROFILE GET
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    //PROFILE UPDATE
    Route::put('/profile', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($request->user()->id)
            ],
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    });

    //CHANGE PASSWORD
    Route::put('/profile/password', function (Request $request) {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    
        $user = $request->user();
    
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }
    
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
    
        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    });


    //Reviews Route
    Route::get('/properties/{property}/reviews', [ReviewController::class, 'index']);
    Route::post('/properties/{property}/reviews', [ReviewController::class, 'store']);

  
});

//----------------------------------------------------- WITHOUT AUTH --------------------------------------------------------------------------------//

// fetch without Auth
Route::get('/properties_view', function () {
    $properties = Property::with('user')
        ->latest()
        ->get()
        ->map(function ($property) {
            // photos and amenities arrays
            $property->photos = $property->photos ?? [];
            $property->amenities = $property->amenities ?? [];
            return $property;
        });

    return response()->json([
        'properties' => $properties,
        'total' => $properties->count()
    ]);
});


// MAP WITHOUT AUTH
Route::get('/properties_map_view/{id}', function ($id) {
    $property = Property::with('user')->findOrFail($id);

    // Get the location details from the 'locations' table
    $location = Location::where('property_id', $id)->first();


    if ($location) {
        $property->location = [
            'lat' => $location->latitude,
            'long' => $location->longitude,
            'location_name' => $location->location_name,
        ];
    } else {
        // If no location is found, return a default empty location
        $property->location = [
            'lat' => null,
            'long' => null,
            'location_name' => null,
        ];
    }

    // Return the property with the location information
    return response()->json($property);
});



Route::post("/login", function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'device_name' => ['required']
    ]);

    $user = User::where('email', $request->email)->where('isActive', 'active')->where('role', 'user')->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect']
        ]);
    }

    // Ensure the User model uses HasApiTokens trait
    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
})->middleware('api');


Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
        'password' => ['required', 'confirmed', Password::defaults()],
        'device_name' => ['required']
    ]);

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'user',
        'isActive' => 'active'
    ]);


    event(new Registered($user));

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
});

Route::post('/forgot-password', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
    ]);


    $status = FacadesPassword::sendResetLink(
        $request->only('email')
    );

    if ($status == FacadesPassword::RESET_LINK_SENT) {
        return back()->with('status', __($status));
    }

    throw ValidationException::withMessages([
        'email' => [trans($status)],
    ]);
});
