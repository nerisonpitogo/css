<?php

use Livewire\Volt\Component;
use App\Models\Office;
use Carbon\Carbon;

new class extends Component {
    public $selType = 'this_week';
    public $dateFrom;
    public $dateTo;
    public $officeDropdowns = [];
    public $selectedOffices = [];
    public $includeSubOffice = 1;

    public $selectedTab = 'overall';

    public function mount()
    {
        // Initialize with top-level offices
        $this->officeDropdowns[0] = Office::where('id', Auth::user()->office_id)
            ->get()
            ->map(function ($office) {
                return [
                    'id' => $office->id,
                    'name' => $office->name,
                ];
            })
            ->toArray();
        $this->updateDates();
        $this->selectedOffices[0] = Auth::user()->office_id;

        $this->updatedSelectedOffices(Auth::user()->office_id, 0);
    }

    public function updateDates()
    {
        $today = Carbon::today();

        switch ($this->selType) {
            case 'this_week':
                $this->dateFrom = $today->startOfWeek()->toDateString();
                $this->dateTo = $today->endOfWeek()->toDateString();
                break;
            case 'last_week':
                $this->dateFrom = $today->subWeek()->startOfWeek()->toDateString();
                $this->dateTo = $today->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->dateFrom = $today->startOfMonth()->toDateString();
                $this->dateTo = $today->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->dateFrom = $today->subMonth()->startOfMonth()->toDateString();
                $this->dateTo = $today->endOfMonth()->toDateString();
                break;
            case 'today':
                $this->dateFrom = $today->toDateString();
                $this->dateTo = $today->toDateString();
                break;
            case 'yesterday':
                $this->dateFrom = $today->subDay()->toDateString();
                $this->dateTo = $today->toDateString();
                break;
            case 'custom':
                $this->dateFrom = '';
                $this->dateTo = '';
                break;
        }
    }

    public function updatedSelectedOffices($value, $key)
    {
        $level = $key + 1;

        // Clear out lower-level selections
        $this->selectedOffices = array_slice($this->selectedOffices, 0, $key + 1);
        $this->officeDropdowns = array_slice($this->officeDropdowns, 0, $level);

        if ($value) {
            // Load child offices for the selected office
            $this->officeDropdowns[$level] = Office::where('parent_id', $value)
                ->get()
                ->map(function ($office) {
                    return [
                        'id' => $office->id,
                        'name' => $office->name,
                    ];
                })
                ->toArray();
            // add "Select Office" to the dropdown
            array_unshift($this->officeDropdowns[$level], ['id' => '', 'name' => 'N/A']);
        }
    }
}; ?>

<div x-cloak>
    <div class="w-full card bg-base-100">
        <div class="card-body">
            <h2 class="text-lg card-title">
                <div class="grid grid-cols-1">
                    <div class="grid grid-cols-2 gap-2 mb-2 md:grid-cols-4 lg:grid-cols-6">

                        @php
                            $selections = [['id' => 1, 'name' => 'Yes'], ['id' => 0, 'name' => 'No']];
                        @endphp
                        <div class="w-full max-w-full col">
                            <x-mary-select class="text-sm" label="Include Sub Offices" :options="$selections"
                                wire:model.live="includeSubOffice" />
                        </div>

                        @foreach ($officeDropdowns as $index => $dropdown)
                            @php
                                $compare = $index == 0 ? 0 : 1;
                            @endphp
                            @if (count($dropdown) > $compare)
                                <div class="col">
                                    <x-mary-select class="text-sm" label="{{ $index == 0 ? 'Office' : 'Sub Office' }}"
                                        :options="$dropdown" wire:model.live="selectedOffices.{{ $index }}" />
                                </div>
                            @endif
                        @endforeach

                    </div>

                    @php
                        $users = [
                            ['id' => 'this_week', 'name' => 'This Week'],
                            ['id' => 'last_week', 'name' => 'Last Week'],
                            ['id' => 'this_month', 'name' => 'This Month'],
                            ['id' => 'last_month', 'name' => 'Last Month'],
                            ['id' => 'today', 'name' => 'Today'],
                            ['id' => 'yesterday', 'name' => 'Yesterday'],
                            ['id' => 'custom', 'name' => 'Custom'],
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 lg:grid-cols-6">
                        <div class="col">
                            <x-mary-select wire:change='updateDates' class="text-sm" label="Type" icon="o-user"
                                :options="$users" wire:model="selType" />
                        </div>
                        <div class="col">
                            <x-mary-datetime class="text-sm" label="Date From" wire:model.live="dateFrom"
                                icon="o-calendar" />
                        </div>
                        <div class="col">
                            <x-mary-datetime class="text-sm" label="Date To" wire:model.live="dateTo"
                                icon="o-calendar" />
                        </div>
                        <div class="items-center justify-center mt-7 col">
                            <x-mary-loading loading-lg wire:loading class="mt-3" />


                            {{-- <x-mary-button wire:loading.remove wire:target='updateDates' label="More Details"
                                class="btn btn-primary" link="{{ route('more-details', ['selType' => $selType]) }}" /> --}}

                            <a target="_blank" class="btn btn-primary"
                                href="{{ route('report', [
                                    'selType' => $selType,
                                    'dateFrom' => $dateFrom,
                                    'dateTo' => $dateTo,
                                    'includeSubOffice' => $includeSubOffice,
                                    'selectedOffices' => $selectedOffices,
                                ]) }}">More
                                Details</a>

                        </div>
                    </div>
                </div>
            </h2>

        </div>
    </div>


    <div class="mt-4">
        <x-mary-tabs class="mt-2" wire:model="selectedTab">
            <x-mary-tab name="overall" label="Overall" icon="o-chart-bar">

                <livewire:dashboard-summary lazy :selType="$selType" :dateFrom="$dateFrom" :dateTo="$dateTo" :includeSubOffice="$includeSubOffice"
                    :$selectedOffices />

                <livewire:dashboard-summary-sqd lazy :selType="$selType" :dateFrom="$dateFrom" :dateTo="$dateTo"
                    :includeSubOffice="$includeSubOffice" :selectedOffices="$selectedOffices" />

            </x-mary-tab>

            <x-mary-tab name="commnets-tab" label="Comments/Suggestions" icon="o-chat-bubble-bottom-center-text">

                <livewire:dashboard-summary-comments lazy :selType="$selType" :dateFrom="$dateFrom" :dateTo="$dateTo"
                    :includeSubOffice="$includeSubOffice" :selectedOffices="$selectedOffices" />

            </x-mary-tab>

            <x-mary-tab name="age-tab" label="Age/Sex/Region" icon="o-list-bullet">

                <livewire:dashboard-summary-age lazy :selType="$selType" :dateFrom="$dateFrom" :dateTo="$dateTo"
                    :includeSubOffice="$includeSubOffice" :selectedOffices="$selectedOffices" />

            </x-mary-tab>
            <x-mary-tab name="services-tab" label="Services Details" icon="o-list-bullet">

                <livewire:dashboard-summary-services lazy :selType="$selType" :dateFrom="$dateFrom" :dateTo="$dateTo"
                    :includeSubOffice="$includeSubOffice" :selectedOffices="$selectedOffices" />

            </x-mary-tab>
        </x-mary-tabs>
    </div>











</div>
