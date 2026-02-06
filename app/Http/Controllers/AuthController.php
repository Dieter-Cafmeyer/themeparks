<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'firstname' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        $user = User::create($fields);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function account() 
    {
        $user = Auth::user();

        return Inertia::render('Dashboard/Account', [
            'user' => $user
        ]);
    }

    public function login(Request $request) 
    {
         $fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($fields)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        $fields = $request->validate([
            'profile_picture' => ['file', 'nullable', 'max:1000'],
            'name' => ['required', 'max:255'],
            'firstname' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'language' => ['required'],
            'tags' => ['nullable'],
        ]);

        if($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $fields['profile_picture'] = Storage::disk('public')->put('profile_pictures', $request->profile_picture);
        }

        $user->update($fields);
        return redirect()->back()->with('success', 'Account updated.');    
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('jobs');
    }
}
