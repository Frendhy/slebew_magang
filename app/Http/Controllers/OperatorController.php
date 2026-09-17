<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    public function index()
    {
        $operators = User::whereIn('role', ['worker', 'foreman'])->get();
        return view('admin.operators.index', compact('operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3',
            'role' => 'required|in:worker,foreman',
            'team' => 'nullable|in:yellow,red,green',
            'pin' => 'nullable|string|max:4',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'team' => $request->team,
            'pin' => $request->pin,
        ]);

        return redirect()->route('admin.operators.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|in:worker,foreman',
            'team' => 'nullable|in:yellow,red,green',
            'pin' => 'nullable|string|max:4',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'team' => $request->team,
            'pin' => $request->pin,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.operators.index')->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.operators.index')->with('success', 'Karyawan berhasil dihapus');
    }
}
