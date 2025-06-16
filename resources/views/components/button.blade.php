@props([
    'type' => 'button',
    'variant' => 'primary', 
    'size' => 'md', 
    'disabled' => false, 
    'fullWidth' => false,
    'icon' => null
])

@php
$variants = [
    'primary' => 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 text-white dark:bg-primary-700 dark:hover:bg-primary-600',
    'secondary' => 'bg-secondary-600 hover:bg-secondary-700 focus:ring-secondary-500 text-white dark:bg-secondary-700 dark:hover:bg-secondary-600',
    'outline-primary' => 'bg-transparent border border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500 dark:border-primary-500 dark:text-primary-500 dark:hover:bg-primary-900/20',
    'outline-secondary' => 'bg-transparent border border-secondary-600 text-secondary-600 hover:bg-secondary-50 focus:ring-secondary-500 dark:border-secondary-500 dark:text-secondary-500 dark:hover:bg-secondary-900/20',
    'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white dark:bg-green-700 dark:hover:bg-green-600',
    'danger' => 'bg-accent-600 hover:bg-accent-700 focus:ring-accent-500 text-white dark:bg-accent-700 dark:hover:bg-accent-600',
    'warning' => 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-400 text-white dark:bg-amber-600 dark:hover:bg-amber-500',
    'neutral' => 'bg-neutral-200 hover:bg-neutral-300 focus:ring-neutral-400 text-neutral-800 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-200',
    'ghost' => 'bg-transparent hover:bg-neutral-100 focus:ring-neutral-400 text-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800/50',
];

$sizes = [
    'xs' => 'px-2.5 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$buttonClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-neutral-900 ' . 
                ($variants[$variant] ?? $variants['primary']) . ' ' .
                ($sizes[$size] ?? $sizes['md']) . ' ' .
                ($disabled ? 'opacity-60 cursor-not-allowed' : '') . ' ' .
                ($fullWidth ? 'w-full' : '');
@endphp

<button 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }} 
    {{ $attributes->merge(['class' => $buttonClasses]) }}
>
    @if($icon)
        <span class="mr-2">
            {!! $icon !!}
        </span>
    @endif
    {{ $slot }}
</button> 