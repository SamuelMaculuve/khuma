<div class="p-6 max-w-md mx-auto">

    <h1 style="font-size:22px;font-weight:700;color:#333;margin-bottom:24px;">Confirmar Pagamento</h1>

    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:24px;">

        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;">
            <span style="color:#555;">Método de Pagamento</span>
            <strong style="color:#2c6fad;">M-Pesa</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;">
            <span style="color:#555;">Plano</span>
            <strong style="color:#333;">{{ $plan->name }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;">
            <span style="color:#555;">Preço</span>
            <strong>{{ number_format($plan->currentPrice()->amount, 2) }} MZN</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;">
            <span style="color:#555;">IVA (16%)</span>
            <strong>{{ number_format($plan->currentPrice()->amount * 0.16, 2) }} MZN</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:14px 0;font-size:16px;font-weight:700;">
            <span>Total</span>
            <span style="color:#2c6fad;">{{ number_format($plan->currentPrice()->amount * 1.16, 2) }} MZN</span>
        </div>

        <div style="margin-top:16px;">
            <input type="tel" wire:model="phone" placeholder="Número M-Pesa (ex: 84xxxxxxx)"
                   style="width:100%;border:1px solid #d9d9d9;border-radius:6px;padding:10px 12px;font-size:14px;margin-bottom:8px;" maxlength="9">
            @error('phone')
                <p style="font-size:12px;color:#dc2626;margin-bottom:8px;">{{ $message }}</p>
            @enderror
            <button wire:click="pay" wire:loading.attr="disabled"
                    style="width:100%;padding:12px;background:#2c6fad;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove>Pagar com M-Pesa</span>
                <span wire:loading>A processar...</span>
            </button>
        </div>
    </div>

</div>
