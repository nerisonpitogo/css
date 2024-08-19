<?php

use Livewire\Volt\Component;
use App\Models\Rbac\Permission;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Spatie\Permission\PermissionRegistrar;

new class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $search = '';
    public $modalPermission = false;
    public $transaction = 'add';
    public Permission $selectedPermission;

    public $permission;

    public $description;

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'name', 'label' => 'Permission'], ['key' => 'description', 'label' => 'Description'], ['key' => 'actions', 'label' => '', 'sortable' => false]];

        return [
            'permissions' => $this->getPermissions(),
            'headers' => $table_headers,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function update(Permission $selectedPermission)
    {
        $this->selectedPermission = $selectedPermission;
        $this->transaction = 'edit';
        $this->permission = $selectedPermission->name;
        $this->description = $selectedPermission->description;
        $this->modalPermission = true;
    }

    public function addPermission()
    {
        $this->transaction = 'add';
        $this->modalPermission = true;
    }

    public function getPermissions()
    {
        $query = Permission::orderBy(...array_values($this->sortBy));
        // add the search
        if ($this->search) {
            $query = $query->where('name', 'like', '%' . $this->search . '%');
        }
        $query = $query->paginate(10);
        return $query;
    }

    function save()
    {
        if ($this->transaction == 'add') {
            $this->validate([
                'permission' => 'required|min:3|unique:permissions,name',
                'description' => 'required|min:3',
            ]);

            Permission::create([
                'name' => $this->permission,
                'description' => $this->description,
                'guard_name' => 'web',
            ]);
            // resetpage
            $this->resetPage();
        } else {
            $permission = Permission::find($this->selectedPermission->id);

            // check if there are no same permission name from other permission
            $this->validate([
                'permission' => 'required|min:3|unique:permissions,name,' . $permission->id,
                'description' => 'required|min:3',
            ]);

            $permission->update([
                'name' => $this->permission,
                'description' => $this->description,
            ]);
        }

        // clear spatie cache
        app()
            ->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $this->success($this->transaction == 'add' ? 'Permission saved successfully' : 'Permission updated successfully');
        $this->reset();
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
        $this->success('Permission deleted successfully');
    }
};
?>

<div class="p-4 bg-base-100">
    <div class="flex items-center justify-between mb-2">
        <x-mary-button label="Create Permission" wire:click='addPermission' icon="o-plus" class="btn-primary" />
        <x-mary-input wire:model.live='search' icon="o-magnifying-glass" clearable />
    </div>

    <x-mary-modal wire:model="modalPermission" title="{{ $transaction == 'add' ? 'Add' : 'Edit' }} Permission"
        subtitle="" separator box-class="" persistent>

        <x-mary-form wire:submit="save">
            <x-mary-input label="Permission" wire:model="permission" />
            <x-mary-input label="Description" wire:model="description" />

            <x-slot:actions>

                <x-mary-button @click="$wire.modalPermission = false" label="Cancel" />
                <x-mary-button label="{{ $transaction == 'add' ? 'Add' : 'Edit' }} Permission" class="btn-primary"
                    type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>


    <div style="position: relative;">
        <!-- Loading Overlay -->
        <div wire:loading.flex wire:target='gotoPage,nextPage,previousPage,delete,save,sortBy,search'
            class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-base-100 bg-opacity-70">
            <x-mary-loading class="text-primary loading-lg" />
        </div>

        <!-- Your Existing Table -->
        <x-mary-table :headers="$headers" :rows="$permissions" :sort-by="$sortBy" with-pagination>

            @php
                $perPage = $permissions->perPage();
                $currentPage = $permissions->currentPage();
                $indexOffset = ($currentPage - 1) * $perPage;
            @endphp
            @scope('cell_counter', $permission, $loop, $indexOffset)
                {{ $loop->index + 1 + $indexOffset }}
            @endscope


            @scope('actions', $permission)
                <div class="flex items-center">
                    <x-mary-button spinner='update({{ $permission->id }})' icon="o-pencil-square"
                        wire:click='update({{ $permission->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                    <x-mary-button wire:click='delete({{ $permission->id }})' wire:confirm='Are you sure to delete?'
                        spinner="delete({{ $permission->id }})" icon="o-trash" class="btn-sm btn-warning"></x-mary-button>
                </div>
            @endscope


        </x-mary-table>
    </div>



</div>
