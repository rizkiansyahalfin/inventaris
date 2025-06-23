@props(['for', 'value'])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-semibold text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label> 