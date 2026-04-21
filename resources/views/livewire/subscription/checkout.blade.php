<div class="p-6 max-w-xl mx-auto">

    <h1 style="font-size:22px;font-weight:700;color:#333;margin-bottom:24px;">Confirmar Subscrição</h1>

    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:24px;">
        <h2 style="font-size:16px;font-weight:600;color:#333;margin-bottom:16px;">Plano {{ $plan->name }}</h2>

        <ul style="list-style:none;padding:0;margin:0 0 16px;font-size:13px;color:#555;line-height:1.8;">
            @foreach ($plan->features as $feature)
                <li>• {{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}: {{ $feature->feature_value }}</li>
            @endforeach
        </ul>

        <div style="margin-top:20px;">
            <button wire:click="subscribe"
                    style="width:100%;padding:12px;background:#2c6fad;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">
                Efetuar Pagamento
            </button>
        </div>
    </div>

</div>
