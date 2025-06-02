<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserForm extends Component
{
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $roles = [];
    public $isEdit; // ✅ This must be PUBLIC

    public function mount($isEdit = false, $userId = null)
    {
        $this->isEdit = $isEdit; // ✅ Assigning value to the property
        $this->roles = Role::whereNot('name', 'superadministrator')->get();

        if ($this->isEdit && $userId) {
            $user = User::findOrFail($userId);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->roles->first()?->id;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : $user->password,
            ]);
            $user->syncRoles([$this->role_id]);
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->addRole($this->role_id);
        }

        session()->flash('message', $this->isEdit ? 'User updated successfully!' : 'User created successfully!');
        // return $this->redirect('/dashboard/users', true);

        // $this->dispatch('redirect', route('dashboard.users.index'));
        return $this->redirect(route('dashboard.users.index'), true);


    }

    public function render()
    {
        return view('livewire.user-form')->layout('layouts.app');
    }
}
