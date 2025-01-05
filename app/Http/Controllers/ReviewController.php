<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Property $property)
    {
        $reviews = Review::with('user')
            ->where('property_id', $property->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }

    public function store(Request $request, Property $property)
    {
        $request->validate([
            'rating' => 'required|string|max:255',
            'comment' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        // Check if user has already reviewed this property
        $existingReview = Review::where('property_id', $property->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this property'
            ], 422);
        }

        $review = Review::create([
            'property_id' => $property->id,
            'user_id' => $request->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ], 201);
    }
}