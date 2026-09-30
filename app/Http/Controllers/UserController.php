<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $roleMap = [
                'IT' => 'admin',
                'Sales' => 'agent',
                'Finance' => 'accountant',
                'Client' => 'client',
            ];
            if (isset($roleMap[$request->department])) {
                $query->where('role', $roleMap[$request->department]);
            }
        }

        if ($request->filled('status')) {
            if ($request->status === 'Inactive') {
                $query->whereRaw('1 = 0');
            }
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('users', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|string|max:50',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => strtolower($data['role']),
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Add',
            'module' => 'User',
            'description' => 'Added new user: ' . $data['name'] . ' with role: ' . strtolower($data['role'])
        ]);

        return redirect('/users');
    }

    public function edit(User $user)
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users', compact('users'))
                ->with('editingUser', $user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|max:50',
        ]);

        $user->update($data);

        return redirect('/users');
    }

    public function destroy(User $user)
    {
        $user->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'User',
            'description' => 'Deleted user: ' . $user->name
        ]);

        return redirect('/users');
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('12345678')
        ]);

        return redirect('/users');
    }
}