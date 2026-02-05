@props([
    'name_prefix' => 'country_code',
    'name_number' => 'phone',
])

<div class="flex rounded-xl border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">

    <!-- Prefixo do país -->
    <select
        wire:model="{{ $name_prefix }}"
        class="bg-gray-50 px-7 py-2 text-sm border-r border-gray-300 focus:outline-none" required
    >
        <option value="258">🇲🇿 +258</option>
        <option value="55">🇧🇷 +55</option>
        <option value="351">🇵🇹 +351</option>
        <option value="27">🇿🇦 +27</option>
        <option value="244">🇦🇴 +244</option>
        <option value="1">🇺🇸 +1</option>
        <option value="44">🇬🇧 +44</option>
        <option value="33">🇫🇷 +33</option>
        <option value="49">🇩🇪 +49</option>
        <option value="91">🇮🇳 +91</option>
    </select>

    <!-- Número -->
    <input
        type="tel"
        wire:model="{{ $name_number }}"
        placeholder="Número de telefone"
        class="flex-1 px-4 py-2 focus:outline-none"
    >

</div>
