<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use App\Models\User;

class AccountController extends Controller
{
    public function update(Request $request)
    {
    /** @var User|null $user */
    $user = Auth::user();
        if (!$user) return back()->with('error', 'غير مصرح');

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:200',
                // ensure email is unique except for the current user
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        try {
            $user->save();
        } catch (QueryException $e) {
            // return with a friendly error; log if necessary
            return back()->with('error', 'تعذر تحديث البيانات. يرجى المحاولة لاحقًا.');
        }

        return back()->with('success', 'تم تحديث البيانات');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

    /** @var User|null $user */
    $user = Auth::user();
    if (!$user) return back()->with('error', 'غير مصرح');

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', 'تم تحديث كلمة المرور');
    }
}
