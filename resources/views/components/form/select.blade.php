@props([
    'label' => null,
    'id',
    'name',
    'placeholder' => 'Pilih opsi',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'options' => [],
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
            {{ $label }} @if($required)<span class="text-accent-500">*</span>@endif
        </label>
    @endif
    
    <select 
        id="{{ $id }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-neutral-300 dark:border-neutral-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-neutral-800 dark:text-neutral-200 sm:text-sm ' . ($error ? 'border-accent-500 focus:border-accent-500 focus:ring-accent-500' : '')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @if($helper && !$error)
        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $helper }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-xs text-accent-600 dark:text-accent-400">{{ $error }}</p>
    @endif
</div> 