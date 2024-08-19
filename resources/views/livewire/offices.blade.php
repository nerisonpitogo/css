<?php

use Livewire\Volt\Component;
use App\Models\Office;

new class extends Component {
    public $office;
    public $modalOfficeShown = false;

    public $officeName;
    public $shortName;
    public $officeLevel;

    public $searchOffice;

    public function with(): array
    {
        return [
            'offices' => $this->getOffices(),
        ];
    }

    public function getOffices()
    {
        $query = Office::orderBy('name')->with('allChildren');

        if ($this->searchOffice) {
            $query = $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchOffice . '%')->orWhereHas('allChildren', function ($q) {
                    // $q->where('name', 'like', '%' . $this->searchOffice . '%')->orWhereHas('allChildren', function ($q) {
                    //     $q->where('name', 'like', '%' . $this->searchOffice . '%');
                    // });
                });
            });
        } else {
            $query = $query->whereNull('parent_id');
        }

        return $query->simplePaginate(5);
    }

    public function saveOffice()
    {
        $this->validate([
            'officeName' => 'required',
            'shortName' => 'required',
            'officeLevel' => 'required',
        ]);

        Office::create([
            'name' => $this->officeName,
            'short_name' => $this->shortName,
            'office_level' => $this->officeLevel,
        ]);

        $this->reset('officeName', 'shortName', 'officeLevel');
        $this->modalOfficeShown = false;
    }
}; ?>

<div>
    <div style="position: relative">



        <div class="container p-4 mx-auto">
            <div class="max-w-full p-6 mx-auto rounded shadow-md bg-base-100">

                <x-mary-header title="Offices">
                    <x-slot:middle class="!justify-end">
                        <x-mary-input icon="o-bolt" placeholder="Search..." wire:model.live="searchOffice" clearable />
                    </x-slot:middle>
                    <x-slot:actions>
                        <x-mary-button icon="o-plus" @click="$wire.modalOfficeShown = true" class="btn-success" />
                    </x-slot:actions>
                </x-mary-header>

                <ul class="space-y-2">
                    @foreach ($offices as $office)
                        <livewire:office-list :office="$office" :key="$office->id" @office_deleted="$refresh" />
                    @endforeach
                </ul>

                <div class="mt-4">
                    {{ $offices->links() }}

                </div>
            </div>
        </div>
    </div>


    <x-mary-modal wire:model="modalOfficeShown">
        <x-mary-form wire:submit="saveOffice">
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
                <x-mary-button @click="$wire.modalOfficeShown = false" label="Cancel" />
                <x-mary-button spinner="saveOffice" type="submit" label="Save" class="btn-primary" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>


</div>
