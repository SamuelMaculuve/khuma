@props([
    'model' => 'prompt',
    'placeholder' => 'Exemplo: Somos uma mercearia chamda Maza Merch. O chatbot deve ser amigável, responder dúvidas sobre produtos, preços e horários.
Produtos e precos:

Saco de batatas - 300 MZN
Saco de cebola - 250 MZN
...
Horario de funcionamento:
segunda-feira - 08h - 16h
quarta-feira - 08h - 16h
...',

 'myclass' => 'class'
])


<div x-data="{ count: 0 }" class="{{ $myclass }}">
<label class="text-start text-gray-600">Descreva a tua Empresa</label>
<textarea
    wire:model.defer="{{ $model }}"
    x-on:input="count = $event.target.value.length"
    maxlength="500"
    rows="8"
    placeholder="{{ $placeholder }}"
    class="w-full resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500"
></textarea>

    <div class="text-right text-xs text-gray-400 mt-1">
        <span x-text="count"></span>/500
    </div>

</div>
