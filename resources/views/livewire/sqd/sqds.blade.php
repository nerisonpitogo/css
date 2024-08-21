<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\Sqd\Sqd;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\Sqd\SqdForm;

new #[Title('Sqds')] class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public $search = '';
    public $modalSqd = false;

    public SqdForm $form;

    public $transaction = 'add';

    public function with(): array
    {
        $table_headers = [
            ['key' => 'counter', 'label' => '#', 'sortable' => false],
            ['key' => 'office_id', 'label' => 'Office Id'],
            ['key' => 'language', 'label' => 'Language'],
            ['key' => 'is_onsite', 'label' => 'Is Onsite'],
            ['key' => 'header', 'label' => 'Header'],
            ['key' => 'client_type', 'label' => 'Client Type'],
            ['key' => 'citizen', 'label' => 'Citizen'],
            ['key' => 'business', 'label' => 'Business'],
            ['key' => 'government', 'label' => 'Government'],
            ['key' => 'date', 'label' => 'Date'],
            ['key' => 'sex', 'label' => 'Sex'],
            ['key' => 'male', 'label' => 'Male'],
            ['key' => 'female', 'label' => 'Female'],
            ['key' => 'age', 'label' => 'Age'],
            ['key' => 'region', 'label' => 'Region'],
            ['key' => 'sqd0', 'label' => 'Sqd0'],
            ['key' => 'sqd1', 'label' => 'Sqd1'],
            ['key' => 'sqd2', 'label' => 'Sqd2'],
            ['key' => 'sqd3', 'label' => 'Sqd3'],
            ['key' => 'sqd4', 'label' => 'Sqd4'],
            ['key' => 'sqd5', 'label' => 'Sqd5'],
            ['key' => 'sqd6', 'label' => 'Sqd6'],
            ['key' => 'sqd7', 'label' => 'Sqd7'],
            ['key' => 'sqd8', 'label' => 'Sqd8'],
            ['key' => 'cc1', 'label' => 'Cc1'],
            ['key' => 'cc1_1', 'label' => 'Cc1 1'],
            ['key' => 'cc1_2', 'label' => 'Cc1 2'],
            ['key' => 'cc1_3', 'label' => 'Cc1 3'],
            ['key' => 'cc1_4', 'label' => 'Cc1 4'],
            ['key' => 'cc2', 'label' => 'Cc2'],
            ['key' => 'cc2_1', 'label' => 'Cc2 1'],
            ['key' => 'cc2_2', 'label' => 'Cc2 2'],
            ['key' => 'cc2_3', 'label' => 'Cc2 3'],
            ['key' => 'cc2_4', 'label' => 'Cc2 4'],
            ['key' => 'cc2_5', 'label' => 'Cc2 5'],
            ['key' => 'cc3', 'label' => 'Cc3'],
            ['key' => 'cc3_1', 'label' => 'Cc3 1'],
            ['key' => 'cc3_2', 'label' => 'Cc3 2'],
            ['key' => 'cc3_3', 'label' => 'Cc3 3'],
            ['key' => 'cc3_4', 'label' => 'Cc3 4'],
            ['key' => 'suggestion', 'label' => 'Suggestion'],
            ['key' => 'email_address', 'label' => 'Email Address'],
            ['key' => 'actions', 'label' => '', 'sortable' => false],
        ];

        return [
            'sqds' => $this->getSqds(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getSqds()
    {
        $query = Sqd::orderBy(...array_values($this->sortBy));
        if ($this->search) {
            $query = $query->whereAny(['office_id', 'language', 'is_onsite', 'header', 'client_type', 'citizen', 'business', 'government', 'date', 'sex', 'male', 'female', 'age', 'region', 'sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8', 'cc1', 'cc1_1', 'cc1_2', 'cc1_3', 'cc1_4', 'cc2', 'cc2_1', 'cc2_2', 'cc2_3', 'cc2_4', 'cc2_5', 'cc3', 'cc3_1', 'cc3_2', 'cc3_3', 'cc3_4', 'suggestion', 'email_address'], 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(10);
        return $query;
    }

    public function saveSqd()
    {
        if ($this->isDuplicateSqd()) {
            $this->addError('form.office_id', 'Office Id already exists');
            return;
        }

        if ($this->transaction === 'add') {
            $this->form->store();
            $this->success('Sqd added successfully');
        } else {
            $this->form->update();
            $this->success('Sqd updated successfully');
        }

        $this->form->reset();
        $this->modalSqd = false;
    }

    private function isDuplicateSqd()
    {
        $query = Sqd::where('office_id', $this->form->office_id);

        if ($this->transaction !== 'add') {
            $query->where('id', '!=', $this->form->sqd->id);
        }

        return $query->exists();
    }

    public function delete($id)
    {
        Sqd::find($id)->delete();
        $this->success('Sqd deleted successfully');
    }

    public function edit($id)
    {
        $sqd = Sqd::find($id);
        $this->form->setSqd($sqd);
        $this->transaction = 'edit';
        $this->modalSqd = true;
    }
}; ?>

<div>

    <x-mary-card shadow separator>
        <x-mary-header title="Sqd" subtitle="List of sqds">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live='search' placeholder="Search..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button @click="$wire.modalSqd = true; $wire.transaction = 'add'" tooltip="Add Sqd" icon="o-plus"
                    class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        <div style="position: relative;">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,saveSqd,sortBy,search'
                class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-table :headers="$headers" :rows="$sqds" :sort-by="$sortBy" with-pagination>

                @php
                    $perPage = $sqds->perPage();
                    $currentPage = $sqds->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $sqd, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope




                @scope('actions', $sqd)
                    <div class="flex items-center">
                        <x-mary-button tooltip="Edit" spinner='edit({{ $sqd->id }})' icon="o-pencil-square"
                            wire:click='edit({{ $sqd->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                        <x-mary-button tooltip="Delete" wire:click='delete({{ $sqd->id }})'
                            wire:confirm='Are you sure to delete?' spinner="delete({{ $sqd->id }})" icon="o-trash"
                            class="btn-sm btn-warning"></x-mary-button>
                    </div>
                @endscope



            </x-mary-table>
        </div>

    </x-mary-card>

    <x-mary-modal title="{{ $transaction === 'add' ? 'Add New Service' : 'Update Service' }}" box-class='max-w-2xl'
        wire:model="modalSqd" persistent>

        <div class="grid grid-cols-1 gap-2">

            <div class="col">
                <x-mary-input type='number' label="Office Id" wire:model="form.office_id" />
            </div>
            <div class="col">
                <x-mary-input label="Language" wire:model="form.language" />
            </div>
            <div class="col">
                <x-mary-input type='number' label="Is Onsite" wire:model="form.is_onsite" />
            </div>
            <div class="col">
                <x-mary-input label="Header" wire:model="form.header" />
            </div>
            <div class="col">
                <x-mary-input label="Client Type" wire:model="form.client_type" />
            </div>
            <div class="col">
                <x-mary-input label="Citizen" wire:model="form.citizen" />
            </div>
            <div class="col">
                <x-mary-input label="Business" wire:model="form.business" />
            </div>
            <div class="col">
                <x-mary-input label="Government" wire:model="form.government" />
            </div>
            <div class="col">
                <x-mary-input label="Date" wire:model="form.date" />
            </div>
            <div class="col">
                <x-mary-input label="Sex" wire:model="form.sex" />
            </div>
            <div class="col">
                <x-mary-input label="Male" wire:model="form.male" />
            </div>
            <div class="col">
                <x-mary-input label="Female" wire:model="form.female" />
            </div>
            <div class="col">
                <x-mary-input label="Age" wire:model="form.age" />
            </div>
            <div class="col">
                <x-mary-input label="Region" wire:model="form.region" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd0" wire:model="form.sqd0" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd1" wire:model="form.sqd1" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd2" wire:model="form.sqd2" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd3" wire:model="form.sqd3" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd4" wire:model="form.sqd4" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd5" wire:model="form.sqd5" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd6" wire:model="form.sqd6" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd7" wire:model="form.sqd7" />
            </div>
            <div class="col">
                <x-mary-input label="Sqd8" wire:model="form.sqd8" />
            </div>
            <div class="col">
                <x-mary-input label="Cc1" wire:model="form.cc1" />
            </div>
            <div class="col">
                <x-mary-input label="Cc1 1" wire:model="form.cc1_1" />
            </div>
            <div class="col">
                <x-mary-input label="Cc1 2" wire:model="form.cc1_2" />
            </div>
            <div class="col">
                <x-mary-input label="Cc1 3" wire:model="form.cc1_3" />
            </div>
            <div class="col">
                <x-mary-input label="Cc1 4" wire:model="form.cc1_4" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2" wire:model="form.cc2" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2 1" wire:model="form.cc2_1" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2 2" wire:model="form.cc2_2" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2 3" wire:model="form.cc2_3" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2 4" wire:model="form.cc2_4" />
            </div>
            <div class="col">
                <x-mary-input label="Cc2 5" wire:model="form.cc2_5" />
            </div>
            <div class="col">
                <x-mary-input label="Cc3" wire:model="form.cc3" />
            </div>
            <div class="col">
                <x-mary-input label="Cc3 1" wire:model="form.cc3_1" />
            </div>
            <div class="col">
                <x-mary-input label="Cc3 2" wire:model="form.cc3_2" />
            </div>
            <div class="col">
                <x-mary-input label="Cc3 3" wire:model="form.cc3_3" />
            </div>
            <div class="col">
                <x-mary-input label="Cc3 4" wire:model="form.cc3_4" />
            </div>
            <div class="col">
                <x-mary-input label="Suggestion" wire:model="form.suggestion" />
            </div>
            <div class="col">
                <x-mary-input label="Email Address" wire:model="form.email_address" />
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.modalSqd = false" />
            <x-mary-button class="btn btn-primary" label="{{ $transaction === 'add' ? 'Save' : 'Update' }}"
                wire:click='saveSqd' spinner='saveSqd' />
        </x-slot:actions>
    </x-mary-modal>

</div>
