<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;

class UsersIndex extends Component
{
    public $search = '';
    public $role = '';

    protected $queryString = ['search', 'role'];

    public function render()
    {
        $users = User::whereRoleNot(['superadministrator'])
            ->when($this->search, fn($q) => $q->whenSearch($this->search))
            ->when($this->role, fn($q) => $q->whereRole($this->role))
            ->get();

        $roles = Role::whereRoleNot(['superadministrator'])->pluck('name', 'id');

        return view('livewire.users-index', compact('users', 'roles'))
            ->layout('layouts.app');
    }
}
