<button {{ $attributes->merge(['type' => 'submit', 'class' => 'py-2 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition']) }}>
    {{ $slot }}
</button> 