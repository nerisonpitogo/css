<?php

use Livewire\Volt\Component;

new class extends Component {
    public $office;

    public $officeName;
    public $shortName;
    public $officeLevel;
}; ?>

<div class="ml-4 border-l-2 border-base-300">
    @if ($office->services->isNotEmpty())
        <button class="items-start justify-start w-full h-auto p-2 mt-1 text-left btn btn-sm"
            :class="{
                'btn-outline btn-primary': serViceAvailedOffice !== {{ $office->id }},
                'btn-primary': serViceAvailedOffice === {{ $office->id }},
                'btn w-full': true
            }"
            @click="handleCLickOffice({{ $office->id }})">{{ $office->name }}
        </button>
    @else
        {{--  --}}
        @if ($office->allChildren->isNotEmpty())
            {{-- <button class="btn btn-outline btn-default btn-sm">{{ $office->name }}</button> --}}
            <div class="p-3 mt-2 text-sm badge badge-neutral">{{ $office->name }}</div>
        @endif
    @endif
    @if ($office->children && $office->children->isNotEmpty())

        @foreach ($office->children as $child)
            <livewire:form.form-office-list :office="$child" :key="$child->id" @office_deleted="$refresh" />
        @endforeach

    @endif
</div>
