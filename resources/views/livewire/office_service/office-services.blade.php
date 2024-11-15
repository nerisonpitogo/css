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
            // ->where(['created_by' => Auth::user()->id])
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->service_name,
                ];
            })
            ->toArray();

        // populate selectedRegions
        $found_office_region = false;
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
                $found_office_region = true;
            } else {
                $this->selectedRegions[$region->id] = [
                    'is_included' => false,
                    'is_priority' => false,
                ];
            }
        }

        if ($found_office_region == false) {
            // add all regions since no selected will default to all selected
            foreach ($regions as $region) {
                $this->selectedRegions[$region->id] = [
                    'is_included' => true,
                    'is_priority' => false,
                ];
            }
        }

        // append libservices with id="" name="Select Service"
        array_unshift($this->libServices, ['id' => '', 'name' => 'Select Service']);
    }

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'service_name', 'label' => 'Service'], ['key' => 'service_description', 'label' => 'Description'], ['key' => 'is_simple', 'label' => 'Is Simple?'], ['key' => 'is_external', 'label' => 'External Service'], ['key' => 'is_internal', 'label' => 'Internal Service'], ['key' => 'actions', 'label' => '', 'sortable' => false]];
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
        <x-mary-header title="{{ $office->name }}" subtitle="{!! $office->getHierarchyString() !!}">
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
                @scope('cell_is_internal', $officeservice)
                    {{ $officeservice->is_internal ? 'Yes' : 'No' }}
                @endscope
                @scope('cell_is_external', $officeservice)
                    {{ $officeservice->is_external ? 'Yes' : 'No' }}
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



            <div class="mt-2 col">
                <label>Client Types</label>
                <div class="flex flex-wrap items-center gap-3 mt-2 col">
                    <x-mary-checkbox label="External Client" wire:model="form.is_external" />
                    <x-mary-checkbox label="Internal Client" wire:model="form.is_internal" />
                </div>
            </div>
            {{-- <div class="mt-2 col">
                <label>SQDs Allowed</label>
                <div class="flex flex-wrap items-center gap-3 mt-2 col">
                    <x-mary-checkbox label="SQD0 - Overall Satisfaction" wire:model="form.has_sqd0" />
                    <x-mary-checkbox label="SQD1 - Responsiveness" wire:model="form.has_sqd1" />
                    <x-mary-checkbox label="SQD2 - Reliability" wire:model="form.has_sqd2" />
                    <x-mary-checkbox label="SQD3 - Access and Facilities" wire:model="form.has_sqd3" />
                    <x-mary-checkbox label="SQD4 - Communication" wire:model="form.has_sqd4" />
                    <x-mary-checkbox label="SQD5 - Costs" wire:model="form.has_sqd5" />
                    <x-mary-checkbox label="SQD6 - Integrity" wire:model="form.has_sqd6" />
                    <x-mary-checkbox label="SQD7 - Assurance" wire:model="form.has_sqd7" />
                    <x-mary-checkbox label="SQD8 - Outcome" wire:model="form.has_sqd8" />
                </div>
            </div> --}}
            <div class="mt-2 col">
                <label>Include Costs (Check if the client is required payment to avail this transaction.)</label>
                <div class="flex flex-wrap items-center gap-3 mt-2 col">
                    <x-mary-checkbox label="SQD5 - Costs" wire:model="form.has_sqd5" />
                </div>
            </div>
            <div class="mt-2 col">
                <label>Allow N/A in the selection of the client response.</label>
                <div class="flex flex-wrap items-center gap-3 mt-2 col">
                    <x-mary-checkbox label="Allow N/A" wire:model="form.allow_na" />
                </div>
            </div>

            <div class="mt-2 col">
                <label class="block text-sm font-medium text-gray-700">Is Simple Transaction</label>
                <x-mary-checkbox
                    label="Transactions like small queries and others. Check this if applicable. Most services indicated in the citizen's charter are not simple transactions."
                    wire:model="form.is_simple" class="mt-1" />
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

        @if ($office->hasChildren())
            <div class="col">
                <x-mary-card class="" title="Online Clients - With Sub Offices"
                    subtitle="Use this link for services provided online. The options displayed will include services from the {{ $office->name }} as well as all its sub-offices for clients to choose from."
                    shadow separator>

                    <div x-data="{ link: '{{ url('/form/0/1/1/' . encrypt($office->id)) }}', copied: false }">
                        External Client Form:
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/0/1/1/' . encrypt($office->id)) }}
                        </span>
                    </div>

                    <div class="mt-4" x-data="{ link: '{{ url('/form/0/1/0/' . encrypt($office->id)) }}', copied: false }">
                        Internal Client Form:
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/0/1/0/' . encrypt($office->id)) }}
                        </span>
                    </div>

                </x-mary-card>
            </div>

            <div class="col">
                <x-mary-card class="" title="Onsite Clients - With Sub Offices"
                    subtitle="Use this link for services provided onsite. The options displayed will include services from the {{ $office->name }} as well as all its sub-offices for clients to choose from."
                    shadow separator>

                    <div x-data="{ link: '{{ url('/form/1/1/1/' . encrypt($office->id)) }}', copied: false }">
                        External Client Form:
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>

                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/1/1/1/' . encrypt($office->id)) }}
                        </span>

                    </div>

                    <div class="mt-4" x-data="{ link: '{{ url('/form/1/1/0/' . encrypt($office->id)) }}', copied: false }">
                        Internal Client Form:
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/1/1/0/' . encrypt($office->id)) }}
                        </span>

                    </div>

                </x-mary-card>
            </div>
        @endif
        <div class="col">
            <x-mary-card class="" title="Online Clients - Office Specific"
                subtitle="Use this link for services provided onsite.  Only the services offered by the {{ $office->name }} will be displayed, which will be the available options for clients to choose from."
                shadow separator>

                <div x-data="{ link: '{{ url('/form/0/0/1/' . encrypt($office->id)) }}', copied: false }">
                    External Client Form:
                    @if ($office->hasExternalServices())
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/0/0/1/' . encrypt($office->id)) }}
                        </span>
                    @else
                        <span class="mr-2 text-red-400">
                            No External Services
                        </span>
                    @endif
                </div>

                <div class="mt-4" x-data="{ link: '{{ url('/form/0/0/0/' . encrypt($office->id)) }}', copied: false }">
                    Internal Client Form:
                    @if ($office->hasInternalServices())
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/0/0/0/' . encrypt($office->id)) }}
                        </span>
                    @else
                        <span class="mr-2 text-red-400">
                            No Internal Services
                        </span>
                    @endif
                </div>

            </x-mary-card>
        </div>

        <div class="col">

            <x-mary-card class="" title="Onsite Clients - Office Specific"
                subtitle="Use this link for services provided onsite. Only the services offered by the {{ $office->name }} will be displayed, which will be the available options for clients to choose from."
                shadow separator>

                <div x-data="{ link: '{{ url('/form/1/0/1/' . encrypt($office->id)) }}', copied: false }">
                    External Client Form:
                    @if ($office->hasExternalServices())
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/1/0/1/' . encrypt($office->id)) }}
                        </span>
                    @else
                        <span class="mr-2 text-red-400">
                            No External Services
                        </span>
                    @endif
                </div>

                <div class="mt-4" x-data="{ link: '{{ url('/form/1/0/0/' . encrypt($office->id)) }}', copied: false }">
                    Internal Client Form:
                    @if ($office->hasInternalServices())
                        <button @click="navigator.clipboard.writeText(link).then(() => copied = true)"
                            x-text="copied ? 'Link Copied!' : 'Copy Link'" class="ml-2 btn btn-sm btn-primary">
                        </button>
                        <br>
                        <span class="mr-2 text-blue-500 break-words break-all">
                            {{ url('/form/1/0/0/' . encrypt($office->id)) }}
                        </span>
                    @else
                        <span class="mr-2 text-red-400">
                            No Internal Services
                        </span>
                    @endif
                </div>

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
                            {{-- @foreach ($regions as $region)
                                <tr wire:key='selections-{{ $region->id }}'>
                                    <td>{{ $region->name }} - {{ $region->id }}</td>
                                    <td>
                                        <input wire:model="officeRegions" value="{{ $region->id }}"
                                            type="checkbox" class="checkbox checkbox-primary" />

                                    </td>
                                    <td>
                                        <input wire:model="officePriority" value="{{ $region->id }}"
                                            type="checkbox" class="checkbox checkbox-primary" />
                                    </td>

                                </tr>
                            @endforeach --}}
                            @foreach ($regions as $region)
                                <tr>
                                    <td>{{ $region->name }}</td>
                                    <td>
                                        <x-mary-checkbox label=""
                                            wire:model="selectedRegions.{{ $region->id }}.is_included">
                                            <x-slot:label>
                                                <div class="items-center">
                                                    <div class="mb-[-6px]">

                                                    </div>
                                                </div>
                                            </x-slot:label>
                                        </x-mary-checkbox>

                                    </td>
                                    <td>
                                        <x-mary-checkbox wire:model="selectedRegions.{{ $region->id }}.is_priority">
                                            <x-slot:label>
                                                <div class="items-center">
                                                    <div class="mb-[-6px]">

                                                    </div>
                                                </div>
                                            </x-slot:label>
                                        </x-mary-checkbox>
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
            <x-mary-card class="" title="Form Header Images & Report Headers/Footers"
                subtitle="This Image will appear at the top of the online form." shadow separator>
                <livewire:office-header-image-upload :office="$office" />
            </x-mary-card>
        </div>


    </div>

</div>
