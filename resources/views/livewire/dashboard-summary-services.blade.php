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
        $external_services = $feedbackService->get_external_services_responses($this->dateFrom, $this->dateTo, end_office_id($this->selectedOffices), $this->includeSubOffice);
        $internal_services = $feedbackService->get_internal_services_responses($this->dateFrom, $this->dateTo, end_office_id($this->selectedOffices), $this->includeSubOffice);
        $services_with_no_responses = $feedbackService->get_services_with_no_responses($this->dateFrom, $this->dateTo, end_office_id($this->selectedOffices), $this->includeSubOffice);

        return [
            'external_services' => $external_services,
            'internal_services' => $internal_services,
            'services_with_no_responses' => $services_with_no_responses,
        ];
    }

    public function placeholder()
    {
        return generate_placeholder(1, 1, 'grid grid-cols-1 gap-2', 96);
    }
}; ?>

<div class="mt-2">

    <div wire:loading class="w-full -mt-2">
        {!! generate_placeholder(1, 1, 'grid grid-cols-1 gap-2', 96) !!}
    </div>

    <div wire:loading.remove class="grid grid-cols-1 ">
        <div class=" col">
            <div class="p-4 overflow-x-auto rounded-lg shadow-lg bg-base-100 stat">

                <table class="table w-full border border-collapse table-auto border-base-200">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="px-4 py-2 text-left border border-base-200">External Services</th>
                            <th class="px-4 py-2 text-left border border-base-200">Responses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($external_services) == 0)
                            <tr>
                                <td colspan="2" class="">No data available</td>
                            </tr>
                        @endif
                        @foreach ($external_services as $service)
                            <tr>
                                <td>{{ $service['service_name'] }}</td>
                                <td>{{ $service['total_responses'] }}</td>
                            </tr>
                        @endforeach

                    </tbody>


                    <thead>
                        <tr class="bg-base-200">
                            <th class="px-4 py-2 text-left border border-base-200">Internal Services</th>
                            <th class="px-4 py-2 text-left border border-base-200">Responses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($internal_services) == 0)
                            <tr>
                                <td colspan="2" class="">No data available</td>
                            </tr>
                        @endif
                        @foreach ($internal_services as $service)
                            <tr>
                                <td>{{ $service['service_name'] }}</td>
                                <td>{{ $service['total_responses'] }}</td>
                            </tr>
                        @endforeach

                    </tbody>


                    <thead>
                        <tr class="bg-base-200">
                            <th colspan="2" class="px-4 py-2 text-left border border-base-200">Services with no
                                responses</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($services_with_no_responses) == 0)
                            <tr>
                                <td colspan="2" class="">No data available</td>
                            </tr>
                        @endif
                        @foreach ($services_with_no_responses as $service)
                            <tr>
                                <td colspan='2'>{{ $service['service_name'] }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>



            </div>
        </div>
    </div>
</div>
