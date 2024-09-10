<?php

use Livewire\Volt\Component;
use App\Models\Feedback;
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

    public function mount($selType, $dateFrom, $dateTo, $includeSubOffice)
    {
        $this->selType = $selType;

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->includeSubOffice = $includeSubOffice;
    }

    public function loading()
    {
        $this->isLoading = true;
    }

    public function notLoading()
    {
        $this->isLoading = false;
    }

    public function with()
    {
        return [
            'total_responses' => get_total_responses($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice),
            'cc1_awareness_total' => get_cc1_awareness_total($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice),
            'cc2_visibility_total' => get_cc2_visibility_total($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice),
            'cc3_helpfulness_total' => get_cc3_helpfulness_total($this->dateFrom, $this->dateTo, end($this->selectedOffices), $this->includeSubOffice),
        ];
    }

    public function placeholder()
    {
        return generate_placeholder(4, 1, 'grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-4', 28);
    }
}; ?>

<div class="mt-2">

    <div wire:loading class="w-full">
        {!! generate_placeholder(4, 1, 'grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-4 -mt-2', 28) !!}
    </div>

    <div wire:loading.remove class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-4">

        <div class="shadow stats">
            <div class="stat">
                <div class="stat-title">Total Responses</div>
                <div class="stat-value">{{ $total_responses }}</div>
                <div class="stat-figure text-primary">
                    <x-mary-icon class="w-12 h-12" name="o-users" />
                </div>
            </div>
        </div>
        {{-- AWARENESS --}}
        @php
            $percentage = $total_responses > 0 ? ($cc1_awareness_total / $total_responses) * 100 : 0;
        @endphp
        <div class="shadow stats">
            <div class="stat">
                <div class="stat-title">CC Awareness</div>
                <div class="stat-value {{ get_percentage_color($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                </div>
                <div class="stat-figure text-primary">
                    <x-mary-icon class="w-12 h-12" name="o-light-bulb" />
                </div>
                <div class="stat-desc">{{ $cc1_awareness_total }} out of {{ $total_responses }} </div>
            </div>
        </div>

        {{-- VISIBILITY --}}
        @php
            $percentage = $total_responses > 0 ? ($cc2_visibility_total / $total_responses) * 100 : 0;

        @endphp
        <div class="shadow stats">
            <div class="stat">
                <div class="stat-title">CC Visibility</div>
                <div class="stat-value {{ get_percentage_color($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                </div>
                <div class="stat-figure text-primary">
                    <x-mary-icon class="w-12 h-12" name="o-eye" />
                </div>
                <div class="stat-desc">{{ $cc2_visibility_total }} out of {{ $total_responses }} </div>
            </div>
        </div>

        {{-- HELPFULNESS --}}
        @php
            $percentage = $total_responses > 0 ? ($cc3_helpfulness_total / $total_responses) * 100 : 0;

        @endphp
        <div class="shadow stats">
            <div class="stat">
                <div class="stat-title">CC Helfulness</div>
                <div class="stat-value {{ get_percentage_color($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                </div>
                <div class="stat-figure text-primary">
                    <x-mary-icon class="w-12 h-12" name="o-question-mark-circle" />
                </div>
                <div class="stat-desc">{{ $cc3_helpfulness_total }} out of {{ $total_responses }} </div>
            </div>
        </div>



    </div>


</div>
