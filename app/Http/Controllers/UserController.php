<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Users/Index', [
            'users' => User::where('deleted', '!=', 1)->where('id', '!=', auth()->id())->orderBy('id')->get(),
        ]);
    }

    public function edit(User $user)
    {
        $user->load('favoriteParks');

        return Inertia::render('Dashboard/Users/Edit', [
            'user' => $user->toArray(),
        ]);
    }

    public function update(Request $request, User $user)
    {
         $data = $request->validate([
            'profile_picture' => ['file', 'nullable', 'max:1000'],
            'name' => ['required', 'max:255'],
            'firstname' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['boolean'],
        ]);

        if($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $data['profile_picture'] = Storage::disk('public')->put('profile_pictures', $request->profile_picture);
        }

        $user->update($data);
        return redirect()->back()->with('success', 'User updated');
    }

    public function delete(User $user)
    {

        $user->update([
            'deleted' => 1,
            'deleted_at' => now(),
        ]);

        return redirect()->route('dashboard.users');
    }

    public function overview()
    {
        return Inertia::render('Users', [
            'users' => User::where('deleted', '!=', 1)->orderBy('id')->get(),
            'title' => __('messages.users'),
        ]);
    }
}
