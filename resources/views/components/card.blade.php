@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'padding' => 'normal',
    'hover' => false,
    'actions' => null
])

@php
    $paddingClass = match($padding) {
        'none' => '',
        'sm' => 'p-3',
        'lg' => 'p-6',
        default => 'p-4'
    };
    
    $cardClass = 'bg-white dark:bg-neutral-800 rounded-card border border-neutral-200 dark:border-neutral-700 shadow-card ' . 
                ($hover ? 'transition-shadow hover:shadow-card-hover' : '');
@endphp

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @if($title || $subtitle || $actions)
        <div class="px-4 py-3 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">
                        {{ $title }}
                    </h3>
                @endif
                
                @if($subtitle)
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            
            @if($actions)
                <div class="flex space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 rounded-b-card">
            {{ $footer }}
        </div>
    @endif
</div> 