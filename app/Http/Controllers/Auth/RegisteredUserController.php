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
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'plans' => Plan::with(['features', 'prices'])->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validação
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:30'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Criar empresa
        $companies = Companies::create([
            'name' => $request->company_name,
        ]);

        // 2. Disparar job (em background - não bloqueia)
        try {
            ProvisionTenantMailDomain::dispatch($companies->id)->afterCommit();
        } catch (\Exception $e) {
            Log::error('Erro no job ProvisionTenantMailDomain: ' . $e->getMessage());
            // Continua o fluxo
        }

        // 3. Criar usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'company_id' => $companies->id,
        ]);

        // 4. Atribuir role
        try {
            $role = Role::firstOrCreate(['name' => 'subscriber']);
            $user->assignRole($role);
        } catch (\Exception $e) {
            Log::error('Erro ao atribuir role: ' . $e->getMessage());
            // Continua o fluxo
        }

        // 5. Disparar evento
        event(new Registered($user));

        // 6. Fazer login
        Auth::login($user);

        // 7. Redirecionar
        if ($request->filled('plan_id')) {
            return redirect()->route('subscription.checkout', $request->integer('plan_id'));
        }

        return redirect(route('dashboard', absolute: false));
    }
}