<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithFileUploads;
    use Toast;

    public $office;
    public $header_image;
    public $report_header_image;
    public $report_footer_image;

    public function mount($office)
    {
        $this->office = $office;
    }

    public function save()
    {
        $this->validate([
            'header_image' => 'nullable|image|max:1024', // 1MB Max
            'report_header_image' => 'nullable|image|max:1024', // 1MB Max
            'report_footer_image' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $updated = false;

        if ($this->header_image) {
            Storage::disk('public')->delete('header_images/' . $this->office->header_image);
            $this->header_image->store('header_images', 'public');
            $this->office->header_image = $this->header_image->hashName();
            $updated = true;
        }

        if ($this->report_header_image) {
            Storage::disk('public')->delete('report_header_images/' . $this->office->report_header_image);
            $this->report_header_image->store('report_header_images', 'public');
            $this->office->report_header_image = $this->report_header_image->hashName();
            $updated = true;
        }

        if ($this->report_footer_image) {
            Storage::disk('public')->delete('report_footer_images/' . $this->office->report_footer_image);
            $this->report_footer_image->store('report_footer_images', 'public');
            $this->office->report_footer_image = $this->report_footer_image->hashName();
            $updated = true;
        }

        if ($updated) {
            $this->office->save();
            $this->success('Header Image Updated');
        }
    }
}; ?>

<div>
    @if ($office->isDescendantOf(Auth::user()->office_id))
        <label for="header_image" class="text-lg"
            style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
            Form Header Image
        </label>
        <x-mary-file wire:model="header_image" accept="image/*">
            <img class="max-w-52 img-fluid"
                src="{{ isset($office->header_image) ? asset('storage/header_images/' . $office->header_image) : url('/images/image_placeholder.png') }}">
        </x-mary-file>

        <hr class="mt-5">

        <label for="report_header_image" class="mt-6 text-lg"
            style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
            Report Header Image
        </label>
        <x-mary-file wire:model="report_header_image" accept="image/*">
            <img class="max-w-52 img-fluid"
                src="{{ isset($office->report_header_image) ? asset('storage/report_header_images/' . $office->report_header_image) : url('/images/image_placeholder.png') }}">
        </x-mary-file>

        <hr class="mt-5">
        <label for="report_footer_image" class="mt-6 text-lg"
            style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
            Report Footer Image
        </label>
        <x-mary-file wire:model="report_footer_image" accept="image/*">
            <img class="max-w-52 img-fluid"
                src="{{ isset($office->report_footer_image) ? asset('storage/report_footer_images/' . $office->report_footer_image) : url('/images/image_placeholder.png') }}">
        </x-mary-file>
        <hr class="mt-5">
        <x-mary-button class="mt-2 btn btn-primary" wire:click="save"
            label="{{ isset($office->header_image) ? 'UPDATE' : 'SAVE' }}" spinner="save" />
    @else
        <img class="max-w-xl img-fluid"
            src="{{ isset($office->header_image) ? asset('storage/header_images/' . $office->header_image) : url('/images/image_placeholder.png') }}">
    @endif
</div>
