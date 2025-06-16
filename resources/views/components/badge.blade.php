@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => true,
])

@php
$variants = [
    'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300 border-primary-200 dark:border-primary-700/50',
    'secondary' => 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900/30 dark:text-secondary-300 border-secondary-200 dark:border-secondary-700/50',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-700/50',
    'danger' => 'bg-accent-100 text-accent-800 dark:bg-accent-900/30 dark:text-accent-300 border-accent-200 dark:border-accent-700/50',
    'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-700/50',
    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700/50',
    'neutral' => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-900/30 dark:text-neutral-300 border-neutral-200 dark:border-neutral-700/50',
];

$sizes = [
    'xs' => 'text-xs py-0.5 px-1.5',
    'sm' => 'text-xs py-0.5 px-2',
    'md' => 'text-xs py-1 px-2.5',
    'lg' => 'text-sm py-1 px-3',
];

$roundedClass = $rounded ? 'rounded-full' : 'rounded';

$badgeClasses = 'inline-flex items-center font-medium border ' . 
                ($variants[$variant] ?? $variants['primary']) . ' ' .
                ($sizes[$size] ?? $sizes['md']) . ' ' .
                $roundedClass;
@endphp

<span {{ $attributes->merge(['class' => $badgeClasses]) }}>
    {{ $slot }}
</span> 