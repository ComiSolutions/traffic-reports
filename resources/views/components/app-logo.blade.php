@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Traffic Reports" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-emerald-600 text-white">
            <span class="text-xs font-bold">TR</span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Traffic Reports" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-emerald-600 text-white">
            <span class="text-xs font-bold">TR</span>
        </x-slot>
    </flux:brand>
@endif
