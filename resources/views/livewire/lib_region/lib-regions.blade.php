<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\LibRegion\LibRegion;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\LibRegion\LibRegionForm;

new #[Title('Lib Regions')] class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public $search = '';
    public $modalLibRegion = false;

    public LibRegionForm $form;

    public $transaction = 'add';

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'name', 'label' => 'Name'], ['key' => 'actions', 'label' => '', 'sortable' => false]];

        return [
            'libregions' => $this->getLibRegions(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getLibRegions()
    {
        $query = LibRegion::orderBy(...array_values($this->sortBy));
        if ($this->search) {
            $query = $query->whereAny(['name'], 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(100);
        return $query;
    }

    public function saveLibRegion()
    {
        if ($this->isDuplicateLibRegion()) {
            $this->addError('form.name', 'Name already exists');
            return;
        }

        if ($this->transaction === 'add') {
            $this->form->store();
            $this->success('LibRegion added successfully');
        } else {
            $this->form->update();
            $this->success('LibRegion updated successfully');
        }

        $this->form->reset();
        $this->modalLibRegion = false;
    }

    private function isDuplicateLibRegion()
    {
        $query = LibRegion::where('name', $this->form->name);

        if ($this->transaction !== 'add') {
            $query->where('id', '!=', $this->form->libregion->id);
        }

        return $query->exists();
    }

    public function delete($id)
    {
        LibRegion::find($id)->delete();
        $this->success('LibRegion deleted successfully');
    }

    public function edit($id)
    {
        $libregion = LibRegion::find($id);
        $this->form->setLibRegion($libregion);
        $this->transaction = 'edit';
        $this->modalLibRegion = true;
    }
}; ?>

<div>

    <x-mary-card shadow separator>
        <x-mary-header title="LibRegion" subtitle="List of libregions">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live='search' placeholder="Search..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button @click="$wire.modalLibRegion = true; $wire.transaction = 'add'" tooltip="Add LibRegion"
                    icon="o-plus" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        <div style="position: relative;">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,saveLibRegion,sortBy,search'
                class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-table :headers="$headers" :rows="$libregions" :sort-by="$sortBy" with-pagination>

                @php
                    $perPage = $libregions->perPage();
                    $currentPage = $libregions->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $libregion, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope




                @scope('actions', $libregion)
                    <div class="flex items-center">
                        <x-mary-button tooltip="Edit" spinner='edit({{ $libregion->id }})' icon="o-pencil-square"
                            wire:click='edit({{ $libregion->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                        <x-mary-button tooltip="Delete" wire:click='delete({{ $libregion->id }})'
                            wire:confirm='Are you sure to delete?' spinner="delete({{ $libregion->id }})" icon="o-trash"
                            class="btn-sm btn-warning"></x-mary-button>
                    </div>
                @endscope



            </x-mary-table>
        </div>

    </x-mary-card>

    <x-mary-modal title="{{ $transaction === 'add' ? 'Add New Service' : 'Update Service' }}" box-class='max-w-2xl'
        wire:model="modalLibRegion" persistent>

        <div class="grid grid-cols-1 gap-2">

            <div class="col">
                <x-mary-input label="Name" wire:model="form.name" />
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.modalLibRegion = false" />
            <x-mary-button class="btn btn-primary" label="{{ $transaction === 'add' ? 'Save' : 'Update' }}"
                wire:click='saveLibRegion' spinner='saveLibRegion' />
        </x-slot:actions>
    </x-mary-modal>

</div>
