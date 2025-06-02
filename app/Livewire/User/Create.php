<?php

namespace App\Livewire\User;

use Livewire\Component;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class Create extends Component
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

    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);


            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->addRole($this->role_id);


        session()->flash('message', 'User created successfully!');
        // return $this->redirect('/dashboard/users', true);

        // $this->dispatch('redirect', route('dashboard.users.index'));
        return $this->redirect(route('dashboard.users.index'), true);


    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
