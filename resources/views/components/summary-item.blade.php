@props(['label', 'value', 'alpine_value' => null])

<div class="grid items-center w-full grid-cols-2 gap-6 p-1 leading-none border-y-2">
    <div class="">
        @if ($alpine_value)
            {{-- <span x-text="sqd_language[language].label_na" class="text-xs leading-none"></span> --}}
            <span class="text-sm leading-none" x-text="sqd_language[language].{{ $alpine_value }}"></span>
        @else
            <span class="text-sm leading-none">{{ $label }}</span>
        @endif
    </div>
    <div class="">
        <span class="text-sm leading-none text-success" x-text="{{ $value }}"></span>
    </div>
</div>
