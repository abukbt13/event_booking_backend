<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function createReview(Request $request){

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully.',
            'review' => $review
        ], 201);
    }
    public function showMyReviews()
    {
        $reviews = Review::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
        ]);
    }
    public function ShowAlleviews()
    {
        $reviews = Review::join('users', 'users.id', '=', 'reviews.user_id')
            ->where('reviews.status', 1)
            ->select('reviews.*', 'users.first_name','users.last_name') // add any user fields you want
            ->get();

        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
        ]);
    }

}
