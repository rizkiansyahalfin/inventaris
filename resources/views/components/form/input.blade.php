@props([
    'label' => null,
    'id',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'leadingIcon' => null,
    'trailingIcon' => null,
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
            {{ $label }} @if($required)<span class="text-accent-500">*</span>@endif
        </label>
    @endif
    
    <div class="relative rounded-md">
        @if($leadingIcon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-500">
                {!! $leadingIcon !!}
            </div>
        @endif
        
        <input 
            id="{{ $id }}" 
            name="{{ $name }}" 
            type="{{ $type }}" 
            value="{{ $value }}" 
            placeholder="{{ $placeholder }}" 
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'block w-full rounded-md border-neutral-300 dark:border-neutral-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-neutral-800 dark:text-neutral-200 sm:text-sm ' . 
                               ($leadingIcon ? 'pl-10 ' : '') . 
                               ($trailingIcon ? 'pr-10 ' : '') .
                               ($error ? 'border-accent-500 focus:border-accent-500 focus:ring-accent-500' : '')]) }}
        />
        
        @if($trailingIcon)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-neutral-500">
                {!! $trailingIcon !!}
            </div>
        @endif
    </div>
    
    @if($helper && !$error)
        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $helper }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-xs text-accent-600 dark:text-accent-400">{{ $error }}</p>
    @endif
</div> 