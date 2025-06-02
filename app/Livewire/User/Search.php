<?php

namespace App\Livewire\User;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class Search extends Component
{
    public $search = ''; // Search query

    public function updatedSearch()
    {
        Log::info('Search updated: ' . $this->search);
    // Triggering render manually to update the UI
    // $this->render();
    }

    public function handleSearch()
{
    Log::info('Search updated: ' . $this->search);
    // You can process your search logic here
    // This could trigger the render or filter the data if needed
}

    public function render()
    {
        Log::info('Rendering with search: ' . $this->search);

        $users = User::whereRoleNot(['superadministrator'])->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate();

        return view('livewire.user.search', compact('users'));
    }
}
