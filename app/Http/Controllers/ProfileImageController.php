<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ProfileImageController extends Controller
{
    public function update (Request $request): RedirectResponse
    {
        $request->validate([
            'profile_img' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        $user = Auth::user();
        
        if ($request->hasFile('profile_img'))
        {
            $file = $request->file('profile_img');
            if ($user->profile_img && $user->profile_img !== 'default.jpg') {
                $oldPath = public_path('imgs/profiles/' . $user->profile_img);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('imgs/profiles'), $filename);

            // Update user model
            $user->profile_img = $filename;
            $user->save();
        }
        
        return back()->with('status', 'profile-image-updated');
    }
}
