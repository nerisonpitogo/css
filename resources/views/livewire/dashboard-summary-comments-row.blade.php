<?php

use Livewire\Volt\Component;
use App\Models\Feedback;

new class extends Component {
    public $feedback;

    public function mount($feedback)
    {
        $this->feedback = $feedback;
    }

    public function toggleIsReported($feedback_id)
    {
        $feedback = Feedback::find($feedback_id);
        $feedback->is_reported = !$feedback->is_reported;
        $feedback->save();

        $this->feedback = $feedback;
    }

    public function toggleHighlight($feedback_id)
    {
        $feedback = Feedback::find($feedback_id);
        // either 1 or 3
        $feedback->type = $feedback->type == 1 ? 3 : 1;
        $feedback->save();

        $this->feedback = $feedback;
    }

    public function toggleLowlight($feedback_id)
    {
        $feedback = Feedback::find($feedback_id);
        // either 2 or 3
        $feedback->type = $feedback->type == 2 ? 3 : 2;
        $feedback->save();

        $this->feedback = $feedback;
    }
}; ?>

<div>
    <div class="grid grid-cols-2 gap-4 p-1 border-b border-gray-200">
        <!-- Feedback Comment -->
        <div class="flex items-center col-span-1">
            <p class="text-gray-700">{{ $feedback->suggestions }}</p>
        </div>
        <!-- Action Buttons -->
        <div class="flex items-center justify-end col-span-1 space-x-2">

            <x-mary-button spinner icon="o-clipboard-document-list" wire:click='toggleIsReported({{ $feedback->id }})'
                class="px-1 py-0 {{ $feedback->is_reported == 1 ? 'bg-green-500 text-base-100' : 'bg-base-300' }} rounded hover:bg-green-600" />

            <x-mary-button spinner icon="o-hand-thumb-up" wire:click='toggleHighlight({{ $feedback->id }})'
                class="px-1 py-0 {{ $feedback->type == 1 ? 'bg-green-500 text-base-100' : 'bg-base-300' }} rounded hover:bg-green-600" />

            <x-mary-button spinner icon="o-hand-thumb-down" wire:click='toggleLowlight({{ $feedback->id }})'
                class="px-1 py-0 {{ $feedback->type == 2 ? 'bg-red-500 text-base-100' : 'bg-base-300' }} rounded hover:bg-red-600" />
        </div>
    </div>
</div>
