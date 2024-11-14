<?php

use Livewire\Volt\Component;
use App\Services\FeedbackService;
use Livewire\Attributes\Reactive;

new class extends Component {
    #[Reactive]
    public $selType;

    #[Reactive]
    public $dateFrom;

    #[Reactive]
    public $dateTo;

    #[Reactive]
    public $includeSubOffice;

    #[Reactive]
    public $selectedOffices = [];

    public function mount($selType, $dateFrom, $dateTo, $includeSubOffice, $selectedOffices)
    {
        $this->selType = $selType;

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->includeSubOffice = $includeSubOffice;
        $this->selectedOffices = $selectedOffices;
    }

    public function with(FeedbackService $feedbackService)
    {
        $raw_data = $feedbackService->get_raw_data($this->dateFrom, $this->dateTo, end_office_id($this->selectedOffices), $this->includeSubOffice);
        return [
            'raw_data' => $raw_data,
        ];
    }

    public function placeholder()
    {
        return generate_placeholder(1, 1, 'grid grid-cols-1 gap-2', 96);
    }
}; ?>

<div class="mt-2 ">

    <div wire:loading class="w-full">
        {!! generate_placeholder(1, 1, 'grid grid-cols-1 gap-2', 96) !!}
    </div>

    <div wire:loading.remove class="grid grid-cols-1">
        <div class=" col">
            <div class="p-4 overflow-x-auto rounded-lg shadow-lg bg-base-100 stat">
                <div class="overflow-x-auto">
                    <table class="table table-xs table-pin-rows table-pin-cols">
                        <thead>
                            <tr class="bg-gray-200 font-weight-bold dark:bg-gray-800">
                                <td>Date and Time</td>
                                <td>Type</td>
                                <td>Sex</td>
                                <td>Age</td>
                                <td>Region</td>
                                <td>Office Visited</td>
                                <td>Service</td>
                                <td>CC1</td>
                                <td>C2</td>
                                <td>CC3</td>
                                <td>SQD0</td>
                                <td>SQD1</td>
                                <td>SQD2</td>
                                <td>SQD3</td>
                                <td>SQD4</td>
                                <td>SQD5</td>
                                <td>SQD6</td>
                                <td>SQD7</td>
                                <td>SQD8</td>
                                <td>Comments</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($raw_data as $raw)
                                <livewire:dashboard-raw-data-tr wire:key='{{ $raw->id }}' :raw_data="$raw"
                                    @raw_deleted="$refresh" />
                            @endforeach

                        </tbody>
                </div>




            </div>
        </div>
    </div>
</div>
