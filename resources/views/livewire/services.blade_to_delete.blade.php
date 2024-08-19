<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\Service;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\Services\ServiceForm;
use Livewire\WithFileUploads;

new #[Title('Services')] class extends Component {
    use WithPagination;
    use Toast;
    use WithFileUploads;

    public $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public $search = '';
    public $modalService = false;

    public ServiceForm $form;

    public $transaction = 'add';

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'service_name', 'label' => 'Service'], ['key' => 'photo', 'label' => 'Photo', 'sortable' => false], ['key' => 'actions', 'label' => '', 'sortable' => false]];

        return [
            'services' => $this->getServices(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getServices()
    {
        $query = Service::orderBy(...array_values($this->sortBy));
        if ($this->search) {
            // $query = $query->where('service_name', 'like', '%' . $this->search . '%');
            $query = $query->whereAny(['service_name'], 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(10);
        return $query;
    }

    public function saveService()
    {
        if ($this->isDuplicateService()) {
            $this->addError('form.service_name', 'Service name already exists');
            return;
        }

        if ($this->transaction === 'add') {
            $this->form->store();
            $this->success('Service added successfully');
        } else {
            $this->form->update();
            $this->success('Service updated successfully');
        }

        $this->modalService = false;
        $this->form->reset();
    }

    private function isDuplicateService()
    {
        $query = Service::where('service_name', $this->form->service_name);

        if ($this->transaction !== 'add') {
            $query->where('id', '!=', $this->form->service->id);
        }

        return $query->exists();
    }

    public function delete($id)
    {
        $service = Service::find($id);
        // delete first the file uploaded     $this->photo->store('public/photos');
        Storage::delete('public/photos/' . $service->photo);

        $service->delete();

        $this->success('Service deleted successfully');
    }

    public function edit($id)
    {
        $service = Service::find($id);
        $this->form->setService($service);
        $this->transaction = 'edit';
        $this->modalService = true;
    }
}; ?>

<div>

    <x-mary-card shadow separator>
        <x-mary-header title="Services" subtitle="List of services">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live='search' placeholder="Search..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button
                    @click="$wire.modalService = true; $wire.transaction = 'add'; 
                    $wire.form.reset();"
                    tooltip="Add Service" icon="o-plus" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        <div style="position: relative;">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,saveService,sortBy,search'
                class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <!-- Your Existing Table -->
            <x-mary-table :headers="$headers" :rows="$services" :sort-by="$sortBy" with-pagination>

                @php
                    $perPage = $services->perPage();
                    $currentPage = $services->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $service, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope

                @scope('cell_photo', $service)
                    @if (!$service->photo)
                        none
                    @else
                        <img src="{{ asset('storage/photos/' . $service->photo) }}" class="w-10 h-10 rounded-lg"
                            alt="photo" />
                    @endif
                @endscope

                @scope('actions', $service)
                    <div class="flex items-center">
                        <x-mary-button tooltip="Edit" spinner='edit({{ $service->id }})' icon="o-pencil-square"
                            wire:click='edit({{ $service->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                        <x-mary-button tooltip="Delete" wire:click='delete({{ $service->id }})'
                            wire:confirm='Are you sure to delete?' spinner="delete({{ $service->id }})" icon="o-trash"
                            class="btn-sm btn-warning"></x-mary-button>
                    </div>
                @endscope



            </x-mary-table>
        </div>

    </x-mary-card>

    <x-mary-modal title="{{ $transaction === 'add' ? 'Add New Service' : 'Update Service' }}" box-class='max-w-xl'
        wire:model="modalService" persistent>

        <div class="grid grid-cols-1">
            <x-mary-input label="Service Name" wire:model="form.service_name" />
            <x-mary-file wire:model="form.photo" label="Receipt" accept="image/png, image/jpeg" />
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.modalService = false" />
            <x-mary-button class="btn btn-primary" label="{{ $transaction === 'add' ? 'Save' : 'Update' }}"
                wire:click='saveService' spinner='saveService' />
        </x-slot:actions>
    </x-mary-modal>

</div>
