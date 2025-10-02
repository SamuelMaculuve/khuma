<?php
namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app')]
class Users extends Component
{
    public $search = '';

    public function render()
    {
        $users = User::with('roles')
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.users', compact('users'));
    }
}
