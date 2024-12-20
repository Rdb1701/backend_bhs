<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function saveLocation(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if location already exists for this property
        $existingLocation = Location::where('property_id', $request->property_id)->first();

        if ($existingLocation) {
            // Update existing location
            $existingLocation->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_name' => $request->location_name
            ]);
        } else {
            // Create new location
            Location::create([
                'property_id' => $request->property_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_name' => $request->location_name
            ]);
        }

        // Redirect with success message
        return back()->with('success', 'Location saved successfully!');
    }

    public function showMapPage()
    {
        
        $properties = Property::where('user_id', Auth::id())->get();
        return view('filament.pages.custom-map-builder', compact('properties'));
    }
}

