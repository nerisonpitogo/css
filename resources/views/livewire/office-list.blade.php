<?php

use Livewire\Volt\Component;
use App\Models\Office;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public $office;

    public $officeName;
    public $shortName;
    public $officeLevel;

    public $modalEditOfficeShown = false;

    public function mount(Office $office)
    {
        $this->office = $office;
        $this->officeName = $office->name;
        $this->shortName = $office->short_name;
        $this->officeLevel = $office->office_level;
    }

    public function saveEditOffice()
    {
        $this->validate([
            'officeName' => 'required',
            'shortName' => 'required',
            'officeLevel' => 'required',
        ]);

        $this->office->update([
            'name' => $this->officeName,
            'short_name' => $this->shortName,
            'office_level' => $this->officeLevel,
        ]);

        $this->modalEditOfficeShown = false;
        $this->success('Office updated successfully');
    }

    public function deleteOffice()
    {
        $this->office->delete();
        $this->success('Office deleted successfully');
        $this->dispatch('office_deleted');
    }
}; ?>

<li class="p-2 rounded-md bg-base-100">
    <div class="grid grid-cols-1">
        <div class="flex items-center space-x-2 col">
            <x-mary-icon name="c-arrow-turn-down-right" />
            {{-- @if ($office->parent_id !== null)
                <x-mary-icon name="c-arrow-turn-down-right" />
            @endif --}}
            <span class="font-semibold text-primary">{{ $office->name }}</span>
            @if ($office->short_name)
                <span class="text-sm text-gray-500">({{ $office->short_name }})</span>
            @endif
            @if ($office->office_level)
                <span class="text-xs text-gray-400">
                    <div class="flex items-center">
                        <a wire:navigate href="{{ route('officeservices', ['office_id' => $office->id]) }}">
                            <div class="mr-6 badge badge-info text-base-100">{{ $office->office_level }}
                                -
                                {{ $office->services->count() == 0 ? 'No' : $office->services->count() }} services
                            </div>
                        </a>

                        <livewire:office-add-sub-office :parentOffice="$office" @sub_office_added="$refresh" />
                        <x-mary-button icon="o-pencil-square" @click="$wire.modalEditOfficeShown = true"
                            class="ml-1 mr-1 btn-xs text-primary" tooltip="Edit" />
                        <x-mary-button icon="o-trash" wire:click='deleteOffice' spinner='deleteOffice'
                            class="btn-xs text-error" wire:confirm='Are you sure?' tooltip="Delete This Office" />
                    </div>
                </span>
            @endif
        </div>

    </div>

    @if ($office->children && $office->children->isNotEmpty())
        <ul class="pl-2 mt-2 ml-4 space-y-2 border-l-2 border-base-300">
            @foreach ($office->children as $child)
                <livewire:office-list :office="$child" :key="$child->id" @office_deleted="$refresh" />
            @endforeach
        </ul>
    @endif


    <x-mary-modal wire:model="modalEditOfficeShown">
        <x-mary-form wire:submit="saveEditOffice">
            <div class="grid cols-1">
                <div class="col">
                    <x-mary-input label="Office Name" wire:model="officeName" placeholder="ex. ICT Unit" clearable />
                </div>
                <div class="mt-3 col">
                    <x-mary-input label="Short Name" wire:model="shortName" placeholder="ex. ICTU" clearable />
                </div>
                <div class="mt-3 col">
                    <x-mary-input label="Office Level" wire:model="officeLevel"
                        placeholder="ex. RO Unit, RO Functional Division" clearable />
                </div>
            </div>

            <x-slot:actions>
                <x-mary-button @click="$wire.modalEditOfficeShown = false" label="Cancel" />
                <x-mary-button spinner="saveEditOffice" type="submit" label="Update" class="btn-primary" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</li>
