<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function engineers()
    {
        $engineers = User::whereIn('perfil', ['RB', 'FR', 'AE', 'PR', 'RE'])
            ->select('id', 'name', 'email', 'perfil', 'initials')
            ->orderBy('name')
            ->get();
        
        return response()->json($engineers);
    }

    public function index()
    {
        $users = User::select('id', 'name', 'email', 'perfil', 'role', 'initials')
            ->orderBy('name')
            ->get();
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'perfil' => 'required|string|max:10',
            'initials' => 'required|string|max:10',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string',
            'perfil' => 'sometimes|string|max:10',
            'initials' => 'sometimes|string|max:10',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // No permitir eliminar al gerente de innovación
        if ($user->perfil === 'GI') {
            return response()->json(['message' => 'No se puede eliminar al Gerente de Innovación'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
