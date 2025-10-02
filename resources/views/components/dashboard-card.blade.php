@props(['label', 'value', 'icon' => '📊', 'color' => 'gray'])

<div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $label }}</span>
        <span class="bg-{{ $color }}-100 text-{{ $color }}-600 text-xs px-2 py-1 rounded-full">{{ $icon }}</span>
    </div>
    <p class="mt-3 text-3xl font-bold text-gray-800">{{ $value }}</p>
</div>
