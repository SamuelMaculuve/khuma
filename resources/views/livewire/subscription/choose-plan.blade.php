<div class="p-6 max-w-6xl mx-auto">

    <div class="mb-8">
        <h1 style="font-size:22px;font-weight:700;color:#333;margin-bottom:4px;">Escolha o seu plano</h1>
        <p style="font-size:14px;color:#666;">Selecione o plano adequado para o seu negócio e clique em continuar.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach ($plans as $plan)

            @if ($plan->code == 'ubuntu')
                <div style="background:#fff;border:1px solid {{ $selectedPlanId == $plan->id ? '#2c6fad' : '#e0e0e0' }};border-radius:8px;padding:28px;display:flex;flex-direction:column;transition:border-color .2s;box-shadow:{{ $selectedPlanId == $plan->id ? '0 0 0 3px rgba(44,111,173,.15)' : 'none' }};">
                    <div style="margin-bottom:20px;">
                        <div style="width:44px;height:44px;background:#f0f4f8;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="fas fa-seedling" style="color:#64748b;font-size:18px;"></i>
                        </div>
                        <h3 style="font-size:20px;font-weight:700;color:#333;">{{ $plan->name }}</h3>
                        <p style="font-size:12px;color:#888;margin-top:2px;">Pequenos negócios</p>
                    </div>
                    <div style="margin-bottom:20px;">
                        <span style="font-size:32px;font-weight:700;color:#333;">{{ number_format($plan->currentPrice()->amount, 0) }}</span>
                        <span style="font-size:13px;color:#888;"> MZN/mês + IVA</span>
                        <br><span style="font-size:12px;color:#e55;">Antes <del>3.000 MZN</del></span>
                    </div>
                    <ul style="list-style:none;margin:0 0 24px;padding:0;flex:1;font-size:13px;color:#444;line-height:2;">
                        <li>✅ 2 Membros da equipa</li>
                        <li>✅ 500 linhas no chatbot</li>
                        <li>✅ 1 instância WhatsApp</li>
                        <li style="color:#bbb;">✗ Venda de produtos</li>
                        <li style="color:#bbb;">✗ Mensagens em massa</li>
                    </ul>
                    <button wire:click="selectPlan({{ $plan->id }})"
                            style="width:100%;padding:12px;border-radius:6px;background:{{ $selectedPlanId == $plan->id ? '#2c6fad' : '#fff' }};color:{{ $selectedPlanId == $plan->id ? '#fff' : '#2c6fad' }};border:1.5px solid #2c6fad;font-size:14px;font-weight:600;cursor:pointer;transition:all .15s;">
                        {{ $selectedPlanId == $plan->id ? '✓ Selecionado' : 'Escolher UBUNTU' }}
                    </button>
                </div>
            @endif

            @if ($plan->code == 'baoba')
                <div style="background:#fff;border:2px solid {{ $selectedPlanId == $plan->id ? '#2c6fad' : '#2c6fad' }};border-radius:8px;padding:28px;display:flex;flex-direction:column;box-shadow:{{ $selectedPlanId == $plan->id ? '0 0 0 3px rgba(44,111,173,.15)' : '0 4px 16px rgba(44,111,173,.1)' }};position:relative;">
                    <div style="position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:#2c6fad;color:#fff;font-size:11px;font-weight:700;padding:3px 14px;border-radius:20px;letter-spacing:.04em;">POPULAR</div>
                    <div style="margin-bottom:20px;">
                        <div style="width:44px;height:44px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="fas fa-tree" style="color:#2c6fad;font-size:18px;"></i>
                        </div>
                        <h3 style="font-size:20px;font-weight:700;color:#333;">{{ $plan->name }}</h3>
                        <p style="font-size:12px;color:#888;margin-top:2px;">Empresas em crescimento</p>
                    </div>
                    <div style="margin-bottom:20px;">
                        <span style="font-size:32px;font-weight:700;color:#333;">{{ number_format($plan->currentPrice()->amount, 0) }}</span>
                        <span style="font-size:13px;color:#888;"> MZN/mês + IVA</span>
                    </div>
                    <ul style="list-style:none;margin:0 0 24px;padding:0;flex:1;font-size:13px;color:#444;line-height:2;">
                        <li>✅ Tudo do UBUNTU +</li>
                        <li>✅ 5 Membros da equipa</li>
                        <li>✅ 1.500 linhas no chatbot</li>
                        <li>✅ Venda de produtos/serviços</li>
                        <li>✅ Mensagens em massa</li>
                        <li>✅ 2 instâncias WhatsApp</li>
                    </ul>
                    <button wire:click="selectPlan({{ $plan->id }})"
                            style="width:100%;padding:12px;border-radius:6px;background:{{ $selectedPlanId == $plan->id ? '#1f5fa3' : '#2c6fad' }};color:#fff;border:none;font-size:14px;font-weight:600;cursor:pointer;transition:all .15s;">
                        {{ $selectedPlanId == $plan->id ? '✓ Selecionado' : 'Escolher BAOBÁ' }}
                    </button>
                </div>
            @endif

            @if ($plan->code == 'leao')
                <div style="background:#fff;border:1px solid {{ $selectedPlanId == $plan->id ? '#2c6fad' : '#e0e0e0' }};border-radius:8px;padding:28px;display:flex;flex-direction:column;transition:border-color .2s;box-shadow:{{ $selectedPlanId == $plan->id ? '0 0 0 3px rgba(44,111,173,.15)' : 'none' }};">
                    <div style="margin-bottom:20px;">
                        <div style="width:44px;height:44px;background:#fef9e7;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="fas fa-crown" style="color:#d4ac0d;font-size:18px;"></i>
                        </div>
                        <h3 style="font-size:20px;font-weight:700;color:#333;">{{ $plan->name }}</h3>
                        <p style="font-size:12px;color:#888;margin-top:2px;">Empresas estabelecidas</p>
                    </div>
                    <div style="margin-bottom:20px;">
                        <span style="font-size:26px;font-weight:700;color:#333;">Personalizado</span>
                    </div>
                    <ul style="list-style:none;margin:0 0 24px;padding:0;flex:1;font-size:13px;color:#444;line-height:2;">
                        <li>✅ Tudo do BAOBÁ +</li>
                        <li>✅ Membros ilimitados</li>
                        <li>✅ Chatbot ilimitado</li>
                        <li>✅ WhatsApp Templates API</li>
                        <li>✅ Suporte prioritário</li>
                    </ul>
                    <a href="#"
                       style="display:block;width:100%;padding:12px;border-radius:6px;background:#1a202c;color:#fff;border:none;font-size:14px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;">
                        Falar com Especialista
                    </a>
                </div>
            @endif

        @endforeach
    </div>

    @if ($selectedPlanId)
        <div style="text-align:center;margin-top:32px;">
            <button wire:click="continue"
                    style="padding:14px 40px;background:#2c6fad;color:#fff;border:none;border-radius:6px;font-size:15px;font-weight:600;cursor:pointer;">
                Continuar para pagamento →
            </button>
        </div>
    @endif

</div>
