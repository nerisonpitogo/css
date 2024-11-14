<?php

use Livewire\Volt\Component;
use App\Models\Feedback;

new class extends Component {
    public $raw;

    public function mount($raw_data)
    {
        $this->raw = $raw_data;
    }

    public function deleteFeedback($feedbackId)
    {
        Feedback::find($feedbackId)->delete();
        return $this->dispatch('raw_deleted');
    }
}; ?>

<tr class="hover:bg-gray-200 dark:hover:bg-gray-800">
    <td>{{ $raw->created_at->diffForHumans() }}</td>
    <td>{{ $raw->is_external ? 'External' : 'Internal' }}</td>
    <td>{{ $raw->sex }}</td>
    <td>{{ $raw->age }}</td>
    <td>{{ $raw->region->name }}</td>
    <td>{{ $raw->officeService->office->short_name }}</td>
    <td>{{ $raw->officeService->service->service_name }}</td>
    <td>{{ $raw->cc1 }}</td>
    <td>{{ $raw->cc2 }}</td>
    <td>{{ $raw->cc3 }}</td>
    <td>{{ $raw->sqd0 }}</td>
    <td>{{ $raw->sqd1 }}</td>
    <td>{{ $raw->sqd2 }}</td>
    <td>{{ $raw->sqd3 }}</td>
    <td>{{ $raw->sqd4 }}</td>
    <td>{{ $raw->sqd5 }}</td>
    <td>{{ $raw->sqd6 }}</td>
    <td>{{ $raw->sqd7 }}</td>
    <td>{{ $raw->sqd8 }}</td>
    <td>{{ $raw->suggestions }}</td>
    <td>
        @can('Delete Feedback')
            <x-mary-button icon="o-trash" title="Remove this feedback." wire:confirm="Are you sure to remove this feedback?"
                wire:click='deleteFeedback({{ $raw->id }})' class="btn-sm text-error">
            </x-mary-button>
        @endcan

    </td>
</tr>
