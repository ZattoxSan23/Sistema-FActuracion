<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('perfil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:250',
            'dni' => 'nullable|string|max:15',
        ]);

        $user->update($data);

        return back()->with('success', 'Perfil actualizado');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'password_actual' => 'required|string',
            'password_nuevo' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($data['password_actual'], $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        $user->update(['password' => Hash::make($data['password_nuevo'])]);

        return back()->with('success', 'Contraseña actualizada');
    }
}
