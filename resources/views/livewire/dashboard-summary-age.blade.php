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
        $age_brackets_with_percentage = $feedbackService->get_age_bracket_with_percentage($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice);
        $sex_bracket_with_percentage = $feedbackService->get_sex_bracket_with_percentage($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice);
        $region_responses_with_percentage = $feedbackService->get_region_responses_with_percentage($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice);

        return [
            'age_brackets_with_percentage' => $age_brackets_with_percentage,
            'sex_bracket_with_percentage' => $sex_bracket_with_percentage,
            'region_responses_with_percentage' => $region_responses_with_percentage,
        ];
    }

    public function placeholder()
    {
        return generate_placeholder(1, 1, 'grid grid-cols-1 gap-2', 96);
    }
}; ?>

<div class="mt-2 ">

    <div wire:loading.remove class="grid grid-cols-1 ">
        <div class=" col">
            <div class="p-4 overflow-x-auto rounded-lg shadow-lg bg-base-100 stat">
                <table class="table w-full border border-collapse table-auto border-base-200">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="px-4 py-2 text-left border border-base-200">D1. Age</th>
                            <th class="px-4 py-2 text-left border border-base-200">External</th>
                            <th class="px-4 py-2 text-left border border-base-200">Internal</th>
                            <th class="px-4 py-2 text-left border border-base-200">Overall</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($age_brackets_with_percentage as $age_bracket)
                            <tr class="hover:bg-base-200">
                                {{-- Display the age bracket label --}}
                                <td class="px-4 py-2 border border-base-200">{{ $age_bracket['label'] }}</td>
                                {{-- Display the external count and percentage --}}
                                <td class="px-4 py-2 border border-base-200">{{ $age_bracket['external'] }}
                                    ({{ number_format($age_bracket['percentage_external'], 2) }}%)
                                </td>
                                {{-- Display the internal count and percentage --}}
                                <td class="px-4 py-2 border border-base-200">{{ $age_bracket['internal'] }}
                                    ({{ number_format($age_bracket['percentage_internal'], 2) }}%)</td>
                                {{-- Display the overall count and percentage --}}
                                <td class="px-4 py-2 border border-base-200">{{ $age_bracket['overall'] }}
                                    ({{ number_format($age_bracket['percentage_overall'], 2) }}%)</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr class="bg-base-200">
                            <th class="px-4 py-2 text-left border border-base-200">D2. Sex</th>
                            <th class="px-4 py-2 text-left border border-base-200">External</th>
                            <th class="px-4 py-2 text-left border border-base-200">Internal</th>
                            <th class="px-4 py-2 text-left border border-base-200">Overall</th>
                        </tr>
                        @foreach ($sex_bracket_with_percentage as $sex => $bracket)
                            <tr>
                                <td class="px-4 py-2 text-left border border-gray-200">{{ ucfirst($sex) }}</td>
                                <td class="px-4 py-2 text-left border border-gray-200">
                                    {{ $bracket['external'] }}
                                    ({{ number_format($bracket['percentage_external'], 2) }}%)
                                </td>
                                <td class="px-4 py-2 text-left border border-gray-200">
                                    {{ $bracket['internal'] }}
                                    ({{ number_format($bracket['percentage_internal'], 2) }}%)
                                </td>
                                <td class="px-4 py-2 text-left border border-gray-200">
                                    {{ $bracket['overall'] }}
                                    ({{ number_format($bracket['percentage_overall'], 2) }}%)
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr class="bg-base-200">
                            <th class="px-4 py-2 text-left border border-base-200">D2. Region</th>
                            <th class="px-4 py-2 text-left border border-base-200">External</th>
                            <th class="px-4 py-2 text-left border border-base-200">Internal</th>
                            <th class="px-4 py-2 text-left border border-base-200">Overall</th>
                        </tr>
                        @foreach ($region_responses_with_percentage as $region_id => $data)
                            <tr>
                                <td class="px-4 py-2 border border-gray-200">{{ $data['region_name'] }}</td>
                                <td class="px-4 py-2 border border-gray-200">
                                    {{ $data['internal'] }} ({{ number_format($data['percentage_internal'], 2) }}%)
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    {{ $data['external'] }} ({{ number_format($data['percentage_external'], 2) }}%)
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
