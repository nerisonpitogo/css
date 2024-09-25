<?php

use Livewire\Volt\Component;
use App\Services\FeedbackService;
use Livewire\Attributes\Reactive;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Office;

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
        $feedbacks = $feedbackService->get_response_comments($this->dateFrom, $this->dateTo, end_office_id($this->selectedOffices), $this->includeSubOffice);
        return [
            'feedbacks' => $feedbacks,
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

    <div wire:loading.remove class="grid grid-cols-1 ">
        <div class="col">
            <div class="p-4 overflow-x-auto rounded-lg shadow-lg bg-base-100 stat">
                @foreach ($feedbacks as $feedback)
                    <livewire:dashboard-summary-comments-row :key="$feedback->id" :$feedback />
                @endforeach
            </div>
        </div>
    </div>
</div>
