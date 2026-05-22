<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'full_name' => 'required|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // rol fijo
        $validated['role'] = 'USER';

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'sometimes|string',
            'full_name' => 'sometimes|string|max:255'
        ]);

        if (isset($validated['username'])) {
            $user->username = $validated['username'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        if (isset($validated['full_name'])) {
            $user->full_name = $validated['full_name'];
        }

        $user->save();

        return $user;
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->noContent();
    }

    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'No autenticado'
            ], 401);
        }

        return $user;
    }
}
