<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionTenantMailDomain;
use App\Models\Companies;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'plans' => Plan::with(['features', 'prices'])->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:30'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $companies = Companies::create([
            'name' => $request->company_name,
        ]);

        ProvisionTenantMailDomain::dispatch($companies->id)->afterCommit();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'company_id' => $companies->id,
        ]);

        $user->assignRole(Role::firstOrCreate(['name' => 'subscriber']));

        event(new Registered($user));

        Auth::login($user);

        if ($request->filled('plan_id')) {
            return redirect()->route('subscription.checkout', $request->integer('plan_id'));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
