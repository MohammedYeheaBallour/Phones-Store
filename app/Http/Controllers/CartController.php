<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartQuery = CartItem::with('product');
        if (Auth::check()) {
            $cartQuery->where('user_id', Auth::id());
        } else {
            $cartQuery->where('session_id', $request->session()->getId());
        }
        $items = $cartQuery->get();
        $total = $items->sum(fn ($i) => $i->product ? $i->product->price * $i->quantity : 0);
        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $cartWhere = ['product_id' => $product->id];
        if (Auth::check()) {
            $cartWhere['user_id'] = Auth::id();
        } else {
            $cartWhere['session_id'] = $request->session()->getId();
        }

        $item = CartItem::firstOrNew($cartWhere);
        $item->quantity = ($item->exists ? $item->quantity : 0) + (int) $request->input('quantity', 1);
        $item->save();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'item' => $item->load('product'),
            ]);
        }

        return back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cartItem->update(['quantity' => $quantity]);
        return back()->with('success', 'تم تحديث الكمية');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'تم حذف العنصر من السلة');
    }

    public function apiIndex(Request $request)
    {
        $cartQuery = CartItem::with('product');
        if (Auth::check()) {
            $cartQuery->where('user_id', Auth::id());
        } else {
            $cartQuery->where('session_id', $request->session()->getId());
        }
        $items = $cartQuery->get();
        $total = $items->sum(fn ($i) => $i->product ? $i->product->price * $i->quantity : 0);
        return response()->json(['items' => $items, 'total' => $total]);
    }
}
