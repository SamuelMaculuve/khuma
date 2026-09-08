<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user->load('subscription.plan'),
            'plans' => Plan::orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,active,suspended',
            'plan_id' => 'nullable|exists:plans,id',
            'started_at' => 'nullable|date',
            'renews_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        // Atualiza estado do user
        $user->update(['status' => $request->status]);

        // Atualiza ou cria subscrição
        if ($request->filled('plan_id')) {
            $user->subscription()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_id' => $user->company_id,
                    'plan_id' => $request->integer('plan_id'),
                    'status' => 'active',
                    'started_at' => $request->date('started_at') ?? now(),
                    'renews_at' => $request->date('renews_at') ?? now()->addMonth(),
                ]
            );
        } else {
            $user->subscription()->delete();
        }

        return redirect()->route('users.index')->with('success', 'Dados do utilizador e subscrição atualizados com sucesso!');
    }


}
