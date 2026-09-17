@props([
    'variant' => 'primary', // primary, secondary, outline, danger
    'size' => 'lg', // sm, md, lg, xl
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-900 rounded-xl';
    
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
        'xl' => 'px-10 py-5 text-xl w-full',
    ][$size];

    $variantClasses = [
        'primary' => 'bg-brand-500 hover:bg-brand-400 text-white shadow-md shadow-brand-500/20 focus:ring-brand-400',
        'secondary' => 'bg-slate-700 hover:bg-slate-600 text-white shadow-md focus:ring-slate-500',
        'outline' => 'bg-transparent border-2 border-brand-500 text-brand-400 hover:bg-brand-500 hover:text-white focus:ring-brand-400',
        'danger' => 'bg-red-500 hover:bg-red-400 text-white shadow-md focus:ring-red-400',
    ][$variant];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
    {{ $slot }}
</button>
