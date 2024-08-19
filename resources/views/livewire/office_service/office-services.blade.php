<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\OfficeService\OfficeService;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\OfficeService\OfficeServiceForm;
use App\Models\Office;
use App\Models\LibService\LibService;

new #[Title('Office Services')] class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public $search = '';
    public $modalOfficeService = false;

    public OfficeServiceForm $form;

    public $transaction = 'add';

    public $office;
    public $libServices;

    public $selectedID;

    public function mount(Office $office_id)
    {
        $this->office = $office_id;

        $this->form->office_id = $office_id->id;

        $this->libServices = LibService::all()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->service_name,
                ];
            })
            ->toArray();

        // append libservices with id="" name="Select Service"
        array_unshift($this->libServices, ['id' => '', 'name' => 'Select Service']);
    }

    public function with(): array
    {
        $table_headers = [
            ['key' => 'counter', 'label' => '#', 'sortable' => false],
            ['key' => 'service_name', 'label' => 'Service'],
            ['key' => 'service_description', 'label' => 'Description'],
            // ['key' => 'has_cc', 'label' => 'In Citizen\'s Charter'],
            ['key' => 'actions', 'label' => '', 'sortable' => false],
        ];

        return [
            'officeservices' => $this->getOfficeServices(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getOfficeServices()
    {
        $query = OfficeService::select('office_services.*', 'lib_services.service_name as service_name', 'lib_services.service_description as service_description')
            ->join('lib_services', 'office_services.service_id', '=', 'lib_services.id')
            ->where('office_id', $this->office->id)
            ->orderBy(...array_values($this->sortBy));

        if ($this->search) {
            $query = $query->whereAny(['office_id', 'service_id', 'has_cc'], 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(10);
        return $query;
    }

    public function saveOfficeService()
    {
        if ($this->transaction === 'add') {
            // check for duplicate
            $duplicate = OfficeService::where('office_id', $this->form->office_id)
                ->where('service_id', $this->form->service_id)
                ->first();

            if ($duplicate) {
                $this->addError('form.service_id', 'Service already exists in this office');
                return;
            }

            $this->form->store();
            $this->success('OfficeService added successfully');
        } else {
            // check duplicate
            $duplicate = OfficeService::where('office_id', $this->form->office_id)
                ->where('service_id', $this->form->service_id)
                ->where('id', '!=', $this->selectedID)
                ->first();
            if ($duplicate) {
                $this->addError('form.service_id', 'Service already exists in this office');
                return;
            }

            $this->form->update();
            $this->success('OfficeService updated successfully');
        }

        $this->form->reset();
        $this->form->office_id = $this->office->id;
        $this->modalOfficeService = false;
    }

    public function delete($id)
    {
        OfficeService::find($id)->delete();
        $this->success('OfficeService deleted successfully');
    }

    public function edit($id)
    {
        $officeservice = OfficeService::find($id);
        $this->selectedID = $id;
        $this->form->setOfficeService($officeservice);
        $this->transaction = 'edit';
        $this->modalOfficeService = true;
    }
}; ?>

<div>
    <x-mary-card shadow separator>
        <x-mary-header title="{{ $office->name }}" subtitle="{!! $office->getHierarchyString() !!} | {{ $office->office_level }}">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live='search' placeholder="Search..." />
            </x-slot:middle>
            <x-slot:actions>
                @if ($office->isDescendantOf(Auth::user()->office_id))
                    <x-mary-button @click="$wire.modalOfficeService = true; $wire.transaction = 'add'"
                        tooltip="Add Office Service" icon="o-plus" class="btn-primary" />
                @endif
            </x-slot:actions>

        </x-mary-header>

        <div style="position: relative;">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,saveOfficeService,sortBy,search'
                class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-table :headers="$headers" :rows="$officeservices" :sort-by="$sortBy" with-pagination>

                @php
                    $perPage = $officeservices->perPage();
                    $currentPage = $officeservices->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $officeservice, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope

                {{-- 
                @scope('cell_has_cc', $officeservice)
                    {{ $officeservice->has_cc ? 'Yes' : 'No' }}
                @endscope --}}


                @scope('actions', $officeservice)
                    <div class="flex items-center">
                        <x-mary-button tooltip="Edit" spinner='edit({{ $officeservice->id }})' icon="o-pencil-square"
                            wire:click='edit({{ $officeservice->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                        <x-mary-button tooltip="Delete" wire:click='delete({{ $officeservice->id }})'
                            wire:confirm='Are you sure to delete?' spinner="delete({{ $officeservice->id }})" icon="o-trash"
                            class="btn-sm btn-warning"></x-mary-button>
                    </div>
                @endscope



            </x-mary-table>
        </div>

    </x-mary-card>

    <x-mary-modal title="{{ $transaction === 'add' ? 'Add New Service' : 'Update Service' }}" box-class='max-w-2xl'
        wire:model="modalOfficeService" persistent>

        <div class="grid grid-cols-1 gap-2">

            {{-- <div class="col">
                <x-mary-input type='number' label="Office Id" wire:model="form.office_id" />
            </div> --}}
            <div class="col">
                <x-mary-select label="Service" :options="$libServices" wire:model="form.service_id" />
            </div>
            {{-- <div class="col">
                @php
                    $ccOptions = [['id' => '1', 'name' => 'Yes'], ['id' => '0', 'name' => 'No']];
                @endphp
                <x-mary-select label="Service found in Citizen's Charter" :options="$ccOptions" wire:model="form.has_cc" />
            </div> --}}
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.modalOfficeService = false" />
            <x-mary-button class="btn btn-primary" label="{{ $transaction === 'add' ? 'Save' : 'Update' }}"
                wire:click='saveOfficeService' spinner='saveOfficeService' />
        </x-slot:actions>
    </x-mary-modal>

</div>
