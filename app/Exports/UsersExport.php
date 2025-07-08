<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $role;
    protected $search;

    public function __construct($role = null, $search = null)
    {
        $this->role = $role;
        $this->search = $search;
    }

    public function collection()
    {
        $query = User::whereRoleNot(['superadministrator'])
            ->when($this->role, fn($q) => $q->whereRole($this->role))
            ->when($this->search, fn($q) => $q->whenSearch($this->search))
            ->select('id', 'name', 'email', 'contact_number', 'address', 'active');

        return $query->get()->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Contact Number' => $user->contact_number,
                'Address' => $user->address,
                'Active' => $user->active ? 'Yes' : 'No',
                'Role' => $user->roles->pluck('name')->implode(', '),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Contact Number', 'Address', 'Active', 'Role'];
    }
}
