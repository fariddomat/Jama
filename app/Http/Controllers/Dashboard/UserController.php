<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $users = \App\Models\User::whereRoleNot(['superadministrator'])->get();
        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereRoleNot(['superadministrator'])->get();
        return view('dashboard.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(User::rules());

        $validated['password'] = bcrypt($request->password);

        $user = \App\Models\User::create($validated);
        $user->addRole($request->role_id ?: 2);

        return redirect()->route('dashboard.users.index')->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }

    public function edit($id)
    {
        $roles = Role::whereRoleNot(['superadministrator'])->get();
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('roles', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $validated = $request->validate(User::rules($id));

        $user->syncRoles([$request->role_id ?: 2]);
        $user->update($validated);

        return redirect()->route('dashboard.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('dashboard.users.index')->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('dashboard.users.index')->with('success', 'User restored successfully.');
    }
}
