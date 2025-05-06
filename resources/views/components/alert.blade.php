@props(['type' => 'info', 'message'])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 border border-green-400 text-green-700',
        'error' => 'bg-red-100 border border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border border-yellow-400 text-yellow-700',
        default => 'bg-blue-100 border border-blue-400 text-blue-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "$classes px-4 py-3 rounded relative mb-4"]) }} role="alert">
    <span class="block sm:inline">{{ $message }}</span>
</div>
