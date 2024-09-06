<?php

use Livewire\Volt\Component;
use App\Models\Office;

new class extends Component {
    public $selType = 'this_week';
    public $dateFrom;
    public $dateTo;
    public $officeDropdowns = [];
    public $selectedOffices = [];
    public $includeSubOffice = 1;

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
        $this->selectedOffices[0] = Auth::user()->office_id;
        // trigger the updatedSelectedOffices method to load child offices
        $this->updatedSelectedOffices(Auth::user()->office_id, 0);
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

<div x-data="{

    selType: @entangle('selType'),
    dateFrom: @entangle('dateFrom'),
    dateTo: @entangle('dateTo'),

    updateDates() {
        const today = new Date();

        // Get the start and end of the current week
        const dayOfWeek = today.getDay(); // 0 (Sunday) to 6 (Saturday)
        const startOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - dayOfWeek);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        if (this.selType === 'this_week') {
            this.dateFrom = this.formatDate(startOfWeek);
            this.dateTo = this.formatDate(endOfWeek);
        } else if (this.selType === 'last_week') {
            startOfWeek.setDate(startOfWeek.getDate() - 7);
            endOfWeek.setDate(endOfWeek.getDate() - 7);
            this.dateFrom = this.formatDate(startOfWeek);
            this.dateTo = this.formatDate(endOfWeek);
        } else if (this.selType === 'this_month') {
            // Set start of the current month
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            // Set end of the current month
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0); // 0 automatically sets the last day of the month

            this.dateFrom = this.formatDate(startOfMonth);
            this.dateTo = this.formatDate(endOfMonth);
        } else if (this.selType === 'last_month') {
            // Set start of the previous month
            const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            // Set end of the previous month
            const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0); // 0 automatically sets the last day of the previous month

            this.dateFrom = this.formatDate(startOfLastMonth);
            this.dateTo = this.formatDate(endOfLastMonth);
        }
        //today
        else if (this.selType === 'today') {
            this.dateFrom = this.formatDate(today);
            this.dateTo = this.formatDate(today);
        } else if (this.selType === 'yesterday') {
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            this.dateFrom = this.formatDate(yesterday);
            this.dateTo = this.formatDate(yesterday);
        }
        //custom just empty
        else if (this.selType === 'custom') {
            this.dateFrom = '';
            this.dateTo = '';
        }
    },

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed, so we add 1
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

}" x-init="updateDates()" x-cloak>
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
                            <x-mary-select @change="updateDates" class="text-sm" label="Type" icon="o-user"
                                :options="$users" wire:model="selType" />
                        </div>
                        <div class="col">
                            <x-mary-datetime class="text-sm" label="Date From" wire:model="dateFrom"
                                icon="o-calendar" />
                        </div>
                        <div class="col">
                            <x-mary-datetime class="text-sm" label="Date To" wire:model="dateTo" icon="o-calendar" />
                        </div>
                        <div class="items-center mt-7 col">
                            <x-mary-button class="btn btn-primary" icon="o-funnel" />
                        </div>
                    </div>
                </div>
            </h2>

        </div>
    </div>
</div>
