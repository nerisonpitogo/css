<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use withFileUploads;
    use Toast;

    public $office;
    public $header_image;

    public function mount($office)
    {
        $this->office = $office;
    }

    public function save()
    {
        $this->validate([
            'header_image' => 'required|image|max:1024', // 1MB Max
        ]);

        if ($this->office->header_image) {
            Storage::disk('public')->delete('header_images/' . $this->office->header_image);
        }

        $this->header_image->store('header_images', 'public');

        $this->office->header_image = $this->header_image->hashName();
        $this->office->save();
        $this->success('Header Image Updated');
    }
}; ?>

<div>
    @if ($office->isDescendantOf(Auth::user()->office_id))
        <x-mary-file wire:model="header_image" accept="image/*">

            <img class="max-w-xl img-fluid"
                src="{{ isset($office->header_image) ? asset('storage/header_images/' . $office->header_image) : url('/images/image_placeholder.png') }}"
                alt="Office Header Image">

        </x-mary-file>

        <x-mary-button class="mt-2 btn btn-primary" wire:click="save"
            label="{{ isset($office->header_image) ? 'UPDATE' : 'SAVE' }}" spinner="save" />
    @else
        <img class="max-w-xl img-fluid"
            src="{{ isset($office->header_image) ? asset('storage/header_images/' . $office->header_image) : url('/images/image_placeholder.png') }}"
            alt="Office Header Image">
    @endif
</div>
