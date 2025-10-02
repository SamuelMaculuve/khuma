<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CallLogController extends Controller
{
    public function index22(Request $request)
    {
        if ($request->ajax()) {
            $query = CallLog::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('started_at', fn($row) => optional($row->started_at)->format('d/m/Y H:i'))
                ->addColumn('duration_fmt', fn($row) => $row->duration_seconds . 's')
                ->addColumn('name', fn($row) => $row->raw['name'] ?? 'Não gravado')

                // Badge para tipo
                ->editColumn('type', function ($row) {
                    switch ($row->type) {
                        case 'INCOMING':
                            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Recebida</span>';
                        case 'OUTGOING':
                            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Efetuada</span>';
                        case 'MISSED':
                            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Perdida</span>';
                        default:
                            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">'.e($row->type).'</span>';
                    }
                })

//                ->addColumn('actions', function($row){
//                    $url = route('call_logs.show', $row->id);
//                    return '<a href="'.$url.'" class="btn btn-sm btn-outline-primary">Detalhes</a>';
//                })
                ->rawColumns(['type','actions']) // importante para renderizar HTML
                ->make(true);
        }

        return view('admin.call_logs.index');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $query = CallLog::query();

            // 🔹 Admin vê tudo
            if ($user->hasRole('admin')) {
                // nada a restringir
            } else {
                // 🔹 Subscriber só vê as próprias chamadas
                $query->where('user_id', $user->id);

                // Verifica plano
                $subscription = $user->subscription; // relação hasOne
                if ($subscription) {
                    if ($subscription->plan === 'kuma_essencial') {
                        $query->where('started_at', '>=', now()->subMonths(3))
                            ->limit(200);
                    }
                    // kuma_premium → sem limites
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('started_at', fn($row) => optional($row->started_at)->format('d/m/Y H:i'))
                ->addColumn('duration_fmt', fn($row) => $row->duration_seconds.'s')
                ->addColumn('name', fn($row) => $row->raw['name'] ?? 'Não gravado')

                // Badge de tipo
                ->editColumn('type', function ($row) {
                    return match ($row->type) {
                        'INCOMING' => '<span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Recebida</span>',
                        'OUTGOING' => '<span class="px-2 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Efetuada</span>',
                        'MISSED'   => '<span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Perdida</span>',
                        default    => '<span class="px-2 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">'.e($row->type).'</span>',
                    };
                })

                ->addColumn('actions', function($row){
                    $url = route('call_logs.show', $row->id);
                    return '<a href="'.$url.'" class="btn btn-sm btn-outline-primary">Detalhes</a>';
                })

                ->rawColumns(['type','actions'])
                ->make(true);
        }

        return view('admin.call_logs.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
