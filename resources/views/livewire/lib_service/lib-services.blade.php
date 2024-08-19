<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\LibService\LibService;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\LibService\LibServiceForm;

new #[Title('Lib Services')] class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public $search = '';
    public $modalLibService = false;

    public LibServiceForm $form;

    public $transaction = 'add';

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'service_name', 'label' => 'Service Name'], ['key' => 'service_description', 'label' => 'Service Description'], ['key' => 'actions', 'label' => '', 'sortable' => false]];

        return [
            'libservices' => $this->getLibServices(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getLibServices()
    {
        $query = LibService::orderBy(...array_values($this->sortBy));
        if ($this->search) {
            $query = $query->whereAny(['service_name', 'service_description'], 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(10);
        return $query;
    }

    public function saveLibService()
    {
        if ($this->isDuplicateLibService()) {
            $this->addError('form.service_name', 'Service Name already exists');
            return;
        }

        if ($this->transaction === 'add') {
            $this->form->store();
            $this->success('LibService added successfully');
        } else {
            $this->form->update();
            $this->success('LibService updated successfully');
        }

        $this->form->reset();
        $this->modalLibService = false;
    }

    private function isDuplicateLibService()
    {
        $query = LibService::where('service_name', $this->form->service_name);

        if ($this->transaction !== 'add') {
            $query->where('id', '!=', $this->form->libservice->id);
        }

        return $query->exists();
    }

    public function delete($id)
    {
        LibService::find($id)->delete();
        $this->success('LibService deleted successfully');
    }

    public function edit($id)
    {
        $libservice = LibService::find($id);
        $this->form->setLibService($libservice);
        $this->transaction = 'edit';
        $this->modalLibService = true;
    }
}; ?>

<div>

    <x-mary-card shadow separator>
        <x-mary-header title="LibService" subtitle="List of libservices">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live='search' placeholder="Search..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button @click="$wire.modalLibService = true; $wire.transaction = 'add'" tooltip="Add LibService"
                    icon="o-plus" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        <div style="position: relative;">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,saveLibService,sortBy,search'
                class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-table :headers="$headers" :rows="$libservices" :sort-by="$sortBy" with-pagination>

                @php
                    $perPage = $libservices->perPage();
                    $currentPage = $libservices->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $libservice, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope




                @scope('actions', $libservice)
                    <div class="flex items-center">
                        <x-mary-button tooltip="Edit" spinner='edit({{ $libservice->id }})' icon="o-pencil-square"
                            wire:click='edit({{ $libservice->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                        <x-mary-button tooltip="Delete" wire:click='delete({{ $libservice->id }})'
                            wire:confirm='Are you sure to delete?' spinner="delete({{ $libservice->id }})" icon="o-trash"
                            class="btn-sm btn-warning"></x-mary-button>
                    </div>
                @endscope



            </x-mary-table>
        </div>

    </x-mary-card>

    <x-mary-modal title="{{ $transaction === 'add' ? 'Add New Service' : 'Update Service' }}" box-class='max-w-2xl'
        wire:model="modalLibService" persistent>

        <div class="grid grid-cols-1 gap-2">

            <div class="col">
                <x-mary-input label="Service Name" wire:model="form.service_name" />
            </div>
            <div class="col">
                <x-mary-input label="Service Description" wire:model="form.service_description" />
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.modalLibService = false" />
            <x-mary-button class="btn btn-primary" label="{{ $transaction === 'add' ? 'Save' : 'Update' }}"
                wire:click='saveLibService' spinner='saveLibService' />
        </x-slot:actions>
    </x-mary-modal>

</div>
