<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    
    public function show()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], 
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect!']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

   
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5000'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            
            if ($user->image_path && Storage::disk('public')->exists($user->image_path ) && $user->image_path!= 'profile-avatars/siteprofile.jpg') {
                Storage::disk('public')->delete($user->image_path);
            }

            
            $path = $request->file('avatar')->store('profile-avatars', 'public');
            
            $user->update([
                'image_path' => $path
            ]);
        }

        return back()->with('success', 'Profile picture updated successfully!');
    }


    public function updateCanteen(Request $request, Canteen $canteen)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($canteen->user_id)],
            'username' => ['required', 'string', Rule::unique('users')->ignore($canteen->user_id)],
        ]);

        
        $canteen->update([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        $canteen->user->update([
            'email' => $request->email,
            'username' => $request->username,
        ]);

        return back()->with('success', 'Canteen details updated successfully!');
    }

    
    public function updatePasswordCanteen(Request $request, Canteen $canteen)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $canteen->user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Canteen password updated successfully!');
    }
}
