<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFavorite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['favorites' => []]);
        }
        $user = Auth::user();
        $favorites = UserFavorite::with('product')->where('user_id', $user->id)->get()->map(fn($f) => [
            'id' => $f->product->id,
            'name' => $f->product->name,
            'price' => $f->product->price,
            'img' => $f->product->image ? '/storage/'.$f->product->image : 'https://via.placeholder.com/400x300?text=No+Image',
            'description' => $f->product->description,
        ]);

        return response()->json(['favorites' => $favorites]);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) return response()->json(['ok' => false], 401);
        $data = $request->validate(['product_id' => 'required|exists:products,id']);
        $fav = UserFavorite::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id']
        ]);
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Product $product)
    {
        if (!Auth::check()) return response()->json(['ok' => false], 401);
        UserFavorite::where('user_id', Auth::id())->where('product_id', $product->id)->delete();
        return response()->json(['ok' => true]);
    }
}
