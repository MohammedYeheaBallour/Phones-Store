<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function apiIndex()
    {
        $reviews = Review::latest()->take(20)->get();
        return response()->json(['reviews' => $reviews]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Optional avatar generation could be handled on the client
        $review = Review::create($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'review' => $review], 201);
        }

        return back()->with('success', 'تم إضافة التقييم');
    }
}
