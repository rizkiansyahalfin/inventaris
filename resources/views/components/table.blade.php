@props([
    'headers' => [],
    'rows' => [],
    'striped' => true,
    'hover' => true,
    'clickable' => false,
    'responsive' => true,
    'bordered' => false,
])

<div {{ $attributes->merge(['class' => 'w-full' . ($responsive ? ' overflow-x-auto' : '')]) }}>
    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
        <thead class="bg-neutral-50 dark:bg-neutral-800/70">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
                
                @if($slot->isNotEmpty())
                    <th scope="col" class="relative px-4 py-3.5">
                        <span class="sr-only">Aksi</span>
                    </th>
                @endif
            </tr>
        </thead>
        
        <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse($rows as $row)
                <tr class="
                    @if($striped && $loop->odd) bg-neutral-50 dark:bg-neutral-800/50 @endif
                    @if($hover) hover:bg-neutral-100 dark:hover:bg-neutral-700/50 @endif
                    @if($clickable) cursor-pointer @endif
                    transition-colors duration-150
                ">
                    @foreach($row as $cell)
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300 {{ $bordered ? 'border border-neutral-200 dark:border-neutral-700' : '' }}">
                            {!! $cell !!}
                        </td>
                    @endforeach
                    
                    @if($slot->isNotEmpty())
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium {{ $bordered ? 'border border-neutral-200 dark:border-neutral-700' : '' }}">
                            {{ $slot }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($slot->isNotEmpty() ? 1 : 0) }}" class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        Tidak ada data yang tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div> 