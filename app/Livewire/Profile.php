<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Profile extends Component
{
    public string $name      = '';
    public string $email     = '';
    public string $password  = '';
    public string $password_confirmation = '';
    public bool   $saved     = false;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::id())],
        ]);

        Auth::user()->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->saved = true;
        $this->dispatch('profile-saved');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->password              = '';
        $this->password_confirmation = '';
        $this->dispatch('password-saved');
    }

    public function render()
    {
        $user         = Auth::user();
        $subscription = $user->subscriptions()->with('plan')->active()->latest()->first();
        $payments     = $user->subscriptions()
            ->with(['plan', 'payments'])
            ->latest()
            ->take(3)
            ->get()
            ->flatMap(fn($s) => $s->payments)
            ->take(3);

        return view('livewire.profile.profile', compact('subscription', 'payments'))
            ->layout('layouts.app');
    }
}
