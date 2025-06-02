<?php

namespace App\Livewire\User;

use App\Models\Role;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Edit extends Component
{
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $roles = [];
    public $isEdit; // ✅ This must be PUBLIC

    public function mount($userId = null)
    {
        $this->roles = Role::whereNot('name', 'superadministrator')->get();

            $user = User::findOrFail($userId);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->roles->first()?->id;

    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : $user->password,
            ]);
            $user->syncRoles([$this->role_id]);


        session()->flash('message', 'User updated successfully!');
        // return $this->redirect('/dashboard/users', true);

        // $this->dispatch('redirect', route('dashboard.users.index'));
        return $this->redirect(route('dashboard.users.index'), true);


    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
