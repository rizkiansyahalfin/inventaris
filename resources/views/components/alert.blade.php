@props([
    'type' => 'info',
    'dismissable' => false,
    'title' => null,
    'icon' => null,
])

@php
$types = [
    'info' => 'bg-blue-50 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800/30',
    'success' => 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800/30',
    'warning' => 'bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800/30',
    'error' => 'bg-accent-50 text-accent-800 dark:bg-accent-900/30 dark:text-accent-300 border-accent-200 dark:border-accent-800/30',
];

$icons = [
    'info' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>',
    'success' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>',
    'warning' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>',
    'error' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>',
];

$alertClasses = 'p-4 border rounded-md animate-fade-in ' . ($types[$type] ?? $types['info']);
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-2"
    role="alert" 
    {{ $attributes->merge(['class' => $alertClasses]) }}
>
    <div class="flex">
        <div class="flex-shrink-0">
            @if($icon)
                {!! $icon !!}
            @else
                {!! $icons[$type] !!}
            @endif
        </div>
        
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            
            <div class="text-sm mt-0.5">
                {{ $slot }}
            </div>
        </div>
        
        @if($dismissable)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        @click="show = false" 
                        type="button" 
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="{
                            'bg-blue-100 text-blue-500 hover:bg-blue-200 focus:ring-blue-600 focus:ring-offset-blue-50': '{{ $type }}' === 'info',
                            'bg-green-100 text-green-500 hover:bg-green-200 focus:ring-green-600 focus:ring-offset-green-50': '{{ $type }}' === 'success',
                            'bg-amber-100 text-amber-500 hover:bg-amber-200 focus:ring-amber-600 focus:ring-offset-amber-50': '{{ $type }}' === 'warning',
                            'bg-accent-100 text-accent-500 hover:bg-accent-200 focus:ring-accent-600 focus:ring-offset-accent-50': '{{ $type }}' === 'error',
                        }"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div> 