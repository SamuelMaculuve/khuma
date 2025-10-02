<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,active,suspended',
            'plan'   => 'nullable|in:kuma_essencial,kuma_premium',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        // Atualiza estado do user
        $user->update(['status' => $request->status]);

        // Atualiza ou cria subscrição
        if ($request->filled('plan')) {
            $user->subscription()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan' => $request->plan,
                    'start_date' => $request->start_date ?? now(),
                    'end_date'   => $request->end_date ?? now()->addMonth(),
                ]
            );
        }

        return redirect()->route('users.index')->with('success', 'Dados do utilizador e subscrição atualizados com sucesso!');
    }


}
