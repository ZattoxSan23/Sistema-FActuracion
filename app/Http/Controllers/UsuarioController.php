<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:administrador,cajera,contador',
            'dni' => 'nullable|string|max:15',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:250',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['activo'] = true;

        User::create($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado');
    }

    public function edit(User $user)
    {
        return view('usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'rol' => 'required|in:administrador,cajera,contador',
            'dni' => 'nullable|string|max:15',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:250',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo');
        }
        $user->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado');
    }

    public function toggle(User $user)
    {
        $user->update(['activo' => !$user->activo]);
        return back()->with('success', 'Estado actualizado');
    }
}
