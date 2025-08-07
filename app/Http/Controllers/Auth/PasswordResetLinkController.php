<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        
        // Get ALL users with this email
        $users = User::where('email', $request->email)->get();
        
        // Check if ANY of them are Admins
        $adminExists = $users->contains('role', 'Admin');
        
        if (!$adminExists) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('Only authorized accounts can reset passwords.')]);
        }
        
        // Send reset link ONLY to Admin(s)
        foreach ($users as $user) {
            if ($user->role === 'Admin') {
                Password::sendResetLink(['email' => $user->email]);
            }
        }
        
        return back()->with('status', __('Password reset link sent to Admin accounts.'));
    }
}
