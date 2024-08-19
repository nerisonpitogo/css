<?php

use Livewire\Volt\Component;
use App\Models\Office;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public $parentOffice;

    public $modalAddOfficeShown = false;

    public $officeName;
    public $shortName;
    public $officeLevel;

    public function mount(Office $parentOffice)
    {
        $this->parentOffice = $parentOffice;
    }

    public function saveAddOffice()
    {
        $this->validate([
            'officeName' => 'required',
            'shortName' => 'required',
            'officeLevel' => 'required',
        ]);

        $this->parentOffice->children()->create([
            'name' => $this->officeName,
            'short_name' => $this->shortName,
            'office_level' => $this->officeLevel,
        ]);

        $this->reset('officeName', 'shortName', 'officeLevel');
        $this->modalAddOfficeShown = false;
        $this->success('Sub Office added successfully');
        $this->dispatch('sub_office_added');
    }
}; ?>

<div>

    <x-mary-button icon="o-plus" @click="$wire.modalAddOfficeShown = true" class="btn-xs text-success"
        tooltip="Add Sub Office" />


    <x-mary-modal wire:model="modalAddOfficeShown">
        <x-mary-form wire:submit="saveAddOffice">
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
                <x-mary-button @click="$wire.modalAddOfficeShown = false" label="Cancel" />
                <x-mary-button spinner="saveAddOffice" type="submit" label="Update" class="btn-primary" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

</div>
