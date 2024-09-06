<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Models\OfficeService\OfficeService;
use Livewire\Attributes\{Title};
use App\Livewire\Forms\OfficeService\OfficeServiceForm;
use App\Models\Office;
use App\Models\LibService\LibService;
use App\Models\LibRegion\LibRegion;
use App\Models\OfficeRegion;

new #[Title('Office Services')] class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public $search = '';
    public $modalOfficeService = false;
    public array $expanded = [];

    public OfficeServiceForm $form;

    public $transaction = 'add';

    public $office;
    public $libServices;

    public $selectedID;
    public $selectedRegions = [];

    public function mount(Office $office_id)
    {
        $this->office = $office_id;

        $this->form->office_id = $office_id->id;

        $this->libServices = LibService::orderBy('service_name')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->service_name,
                ];
            })
            ->toArray();

        // populate selectedRegions
        $regions = LibRegion::all();
        foreach ($regions as $region) {
            $officeRegion = OfficeRegion::where('office_id', $office_id->id)
                ->where('region_id', $region->id)
                ->first();

            if ($officeRegion) {
                $this->selectedRegions[$region->id] = [
                    'is_included' => true,
                    'is_priority' => $officeRegion->is_priority ? true : false,
                ];
            } else {
                $this->selectedRegions[$region->id] = [
                    'is_included' => false,
                    'is_priority' => false,
                ];
            }
        }

        // append libservices with id="" name="Select Service"
        array_unshift($this->libServices, ['id' => '', 'name' => 'Select Service']);
    }

    public function with(): array
    {
        $table_headers = [
            ['key' => 'counter', 'label' => '#', 'sortable' => false],
            ['key' => 'service_name', 'label' => 'Service'],
            ['key' => 'service_description', 'label' => 'Description'],
            ['key' => 'is_simple', 'label' => 'Is Simple?'],
            // ['key' => 'has_sqd0', 'label' => 'SQD0'],
            // ['key' => 'has_sqd1', 'label' => 'SQD1'],
            // ['key' => 'has_sqd2', 'label' => 'SQD2'],
            // ['key' => 'has_sqd3', 'label' => 'SQD3'],
            // ['key' => 'has_sqd4', 'label' => 'SQD4'],
            // ['key' => 'has_sqd5', 'label' => 'SQD5'],
            // ['key' => 'has_sqd6', 'label' => 'SQD6'],
            // ['key' => 'has_sqd7', 'label' => 'SQD7'],
            // ['key' => 'has_sqd8', 'label' => 'SQD8'],
            // ['key' => 'allow_na', 'label' => 'Allow N/A'],
            // ['key' => 'has_cc', 'label' => 'In Citizen\'s Charter'],
            ['key' => 'actions', 'label' => '', 'sortable' => false],
        ];

        return [
            'officeservices' => $this->getOfficeServices(),
            'regions' => Libregion::all(),
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

    public function save()
    {
        // dd($this->selectedRegions);

        $regions = LibRegion::all();
        foreach ($regions as $region) {
            // OfficeRegion
            // update or create
            if (isset($this->selectedRegions[$region->id]['is_included']) && $this->selectedRegions[$region->id]['is_included'] === true) {
                $officeRegion = OfficeRegion::updateOrCreate(
                    ['office_id' => $this->office->id, 'region_id' => $region->id],
                    [
                        'is_priority' => $this->selectedRegions[$region->id]['is_priority'] ?? false,
                    ],
                );
            }
        }

        // Filter the selectedRegions to only include regions that are marked as included
        $includedRegionIds = array_keys(
            array_filter($this->selectedRegions, function ($region) {
                return $region['is_included'] === true;
            }),
        );

        // Erase all regions that are not included
        OfficeRegion::where('office_id', $this->office->id)
            ->whereNotIn('region_id', $includedRegionIds)
            ->delete();

        $this->success('Regions updated successfully');
    }
}; ?>

<div x-cloak>
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

            <x-mary-table :headers="$headers" :rows="$officeservices" :sort-by="$sortBy" with-pagination wire:model="expanded"
                expandable>

                @php
                    $perPage = $officeservices->perPage();
                    $currentPage = $officeservices->currentPage();
                    $indexOffset = ($currentPage - 1) * $perPage;
                @endphp
                @scope('cell_counter', $officeservice, $loop, $indexOffset)
                    {{ $loop->index + 1 + $indexOffset }}
                @endscope
                @scope('cell_is_simple', $officeservice)
                    {{ $officeservice->is_simple ? 'Yes' : 'No' }}
                @endscope

                @scope('expansion', $officeservice)
                    <div class="overflow-x-auto">
                        <table class="table table-xs">
                            <tbody>
                                <tr class="bg-base-300">
                                    <td>SQD0</td>
                                    <td>SQD1</td>
                                    <td>SQD2</td>
                                    <td>SQD3</td>
                                    <td>SQD4</td>
                                    <td>SQD5</td>
                                    <td>SQD6</td>
                                    <td>SQD7</td>
                                    <td>SQD8</td>
                                    <td>N/A</td>
                                </tr>
                                <tr>
                                    <td>
                                        @if ($officeservice->has_sqd0)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd1)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd2)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd3)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd4)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd5)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd6)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd7)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->has_sqd8)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($officeservice->allow_na)
                                            <x-mary-icon name="o-check" class="text-success" />
                                        @else
                                            <x-mary-icon name="o-x-mark" class="text-error " />
                                        @endif
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endscope

                {{-- 
                @scope('cell_has_cc', $officeservice)
                    {{ $officeservice->has_cc ? 'Yes' : 'No' }}
                @endscope --}}


                @scope('actions', $officeservice)
                    @if ($officeservice->office->isDescendantOf(Auth::user()->office_id))
                        <div class="flex items-center">
                            <x-mary-button tooltip="Edit" spinner='edit({{ $officeservice->id }})' icon="o-pencil-square"
                                wire:click='edit({{ $officeservice->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                            <x-mary-button tooltip="Delete" wire:click='delete({{ $officeservice->id }})'
                                wire:confirm='Are you sure to delete?' spinner="delete({{ $officeservice->id }})"
                                icon="o-trash" class="btn-sm btn-warning"></x-mary-button>
                        </div>
                    @endif
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

            <div class="col">
                <div class="flex flex-wrap items-center gap-3">
                    <x-mary-checkbox label="Simple Transaction" wire:model="form.is_simple" />
                    <x-mary-checkbox label="SQD0" wire:model="form.has_sqd0" />
                    <x-mary-checkbox label="SQD1" wire:model="form.has_sqd1" />
                    <x-mary-checkbox label="SQD2" wire:model="form.has_sqd2" />
                    <x-mary-checkbox label="SQD3" wire:model="form.has_sqd3" />
                    <x-mary-checkbox label="SQD4" wire:model="form.has_sqd4" />
                    <x-mary-checkbox label="SQD5" wire:model="form.has_sqd5" />
                    <x-mary-checkbox label="SQD6" wire:model="form.has_sqd6" />
                    <x-mary-checkbox label="SQD7" wire:model="form.has_sqd7" />
                    <x-mary-checkbox label="SQD8" wire:model="form.has_sqd8" />
                    <x-mary-checkbox label="Allow N/A" wire:model="form.allow_na" />
                </div>
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

    <div class="grid grid-cols-1 gap-2 mt-2 lg:grid-cols-2">

        <div class="col">
            <x-mary-card class="" title="Onsite - Office Specific"
                subtitle="Use this link for services provided onsite. Only services under the {{ $office->name }} will be displayed."
                shadow separator>
                <span class="text-blue-500">{{ url('/form/1/0/' . $office->id) }}</span>
            </x-mary-card>
        </div>

        <div class="col">
            <x-mary-card class="" title="Onsite - With Sub Offices"
                subtitle="Use this link for services provided onsite. Only services under the {{ $office->name }} will be displayed."
                shadow separator>
                <span class="text-blue-500">{{ url('/form/1/1/' . $office->id) }}</span>
            </x-mary-card>
        </div>

        <div class="col">
            <x-mary-card class="" title="Online - Office Specific"
                subtitle="Use this link for services provided online. Only services under the {{ $office->name }} will be displayed."
                shadow separator>
                <span class="text-blue-500">{{ url('/form/0/0/' . $office->id) }}</span>
            </x-mary-card>
        </div>

        <div class="col">
            <x-mary-card class="" title="Online - With Sub Offices"
                subtitle="Use this link for services provided online. Only services under the {{ $office->name }} will be displayed."
                shadow separator>
                <span class="text-blue-500">{{ url('/form/0/1/' . $office->id) }}</span>
            </x-mary-card>
        </div>

    </div>
    <div class="grid grid-cols-1 gap-2 mt-2 lg:grid-cols-2">
        <div class="col">
            <x-mary-card class="" title="Regions"
                subtitle="This will be the regions that will only appear in the client form. None selected will display all regions. Priority will dispaly at the top most of the list."
                shadow separator>

                <div class="overflow-x-auto">
                    <table class="table table-xs">
                        <thead>
                            <tr>
                                <th>Region</th>
                                <th>Is Included</th>
                                <th>Is Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($regions as $region)
                                <tr>
                                    <td>{{ $region->name }}</td>
                                    <td>
                                        <x-mary-checkbox
                                            wire:model="selectedRegions.{{ $region->id }}.is_included" />
                                    </td>
                                    <td>
                                        <x-mary-checkbox
                                            wire:model="selectedRegions.{{ $region->id }}.is_priority" />
                                    </td>

                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4">
                                    <x-mary-button spinner label="Save" class="btn-primary" wire:click="save" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </x-mary-card>
        </div>



        <div class="col">
            <x-mary-card class="" title="Form Header Image"
                subtitle="This Image will appear at the top of the online form." shadow separator>
                <livewire:office-header-image-upload :office="$office" />
            </x-mary-card>
        </div>


    </div>

</div>
