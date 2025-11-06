<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email:rfc,dns|unique:newsletter_subscriptions,email',
        ]);

        $sub = NewsletterSubscription::create($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'subscription' => $sub], 201);
        }

        return back()->with('success', 'تم الاشتراك في النشرة البريدية');
    }
}
