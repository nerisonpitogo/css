<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Spatie\Permission\PermissionRegistrar;

new class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $search = '';
    public $modalRole = false;
    public $transaction = 'add';
    public Role $selectedRole;
    public $modalPermissions = false;
    public $selectedPermissions = [];

    public $role;

    public $description;

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'name', 'label' => 'Role'], ['key' => 'description', 'label' => 'Description'], ['key' => 'permissions', 'label' => 'Permissions', 'sortable' => false], ['key' => 'actions', 'label' => '', 'sortable' => false]];
        $allPermissions = Permission::orderBy('name')->get();
        return [
            'roles' => $this->getRoles(),
            'headers' => $table_headers,
            'allPermissions' => $allPermissions,
        ];
    }

    public function getPermissions($roleId)
    {
        $this->selectedRole = Role::find($roleId);
        $this->selectedPermissions = $this->selectedRole->permissions->pluck('name')->toArray();
        $this->modalPermissions = true;
        // dd($this->selectedPermissions);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function update(Role $selectedRole)
    {
        $this->selectedRole = $selectedRole;
        $this->transaction = 'edit';
        $this->role = $selectedRole->name;
        $this->description = $selectedRole->description;
        $this->modalRole = true;
    }

    public function addRole()
    {
        $this->transaction = 'add';
        $this->modalRole = true;
    }

    public function getRoles()
    {
        $query = Role::orderBy(...array_values($this->sortBy));
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
                'role' => 'required|min:3|unique:roles,name',
                'description' => 'required|min:3',
            ]);

            Role::create([
                'name' => $this->role,
                'description' => $this->description,
                'guard_name' => 'web',
            ]);
            // resetpage
            $this->resetPage();
        } else {
            $role = Role::find($this->selectedRole->id);

            $this->validate([
                'role' => 'required|min:3|unique:roles,name,' . $role->id,
                'description' => 'required|min:3',
            ]);

            $role->update([
                'name' => $this->role,
                'description' => $this->description,
            ]);
        }

        // clear spatie cache role
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->success($this->transaction == 'add' ? 'Role saved successfully' : 'Role updated successfully');
        $this->reset();
    }

    public function delete(Role $role)
    {
        $role->delete();
        $this->success('Role deleted successfully');
    }

    public function savePermissionsToRole()
    {
        //         array:2 [▼ // resources\views\livewire\rbac\roles.blade.php:120
        //   "permission-0" => true
        //   "permission-1" => true
        // dd($this->selectedPermissions);

        $this->selectedRole->syncPermissions($this->selectedPermissions);
        $this->success('Permissions saved successfully');
        $this->modalPermissions = false;
        $this->selectedPermissions = [];
    }
};
?>

<div class="p-4 bg-base-100">
    <div class="flex items-center justify-between mb-2">
        <x-mary-button label="Create Role" wire:click='addRole' icon="o-plus" class="btn-primary" />
        <x-mary-input wire:model.live='search' icon="o-magnifying-glass" clearable />
    </div>

    <x-mary-modal wire:model="modalRole" title="{{ $transaction == 'add' ? 'Add' : 'Edit' }} Role" subtitle=""
        separator box-class="" persistent>

        <x-mary-form wire:submit="save">
            <x-mary-input label="Role" wire:model="role" />
            <x-mary-input label="Description" wire:model="description" />

            <x-slot:actions>

                <x-mary-button @click="$wire.modalRole = false" label="Cancel" />
                <x-mary-button label="{{ $transaction == 'add' ? 'Add' : 'Edit' }} Role" class="btn-primary"
                    type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>

    {{-- MODAL PERMISSIONS --}}
    <x-mary-modal wire:model="modalPermissions" title="{{ $selectedRole->name ?? '' }}"
        subtitle="{{ $selectedRole->description ?? '' }}" separator box-class="max-w-2xl" persistent>

        <x-mary-form wire:submit="savePermissionsToRole">
            <div class="grid grid-cols-3 gap-4">
                @foreach ($allPermissions as $permission)
                    <x-mary-checkbox wire:model="selectedPermissions" label="{{ $permission->name }} "
                        value="{{ $permission->name }}" />
                @endforeach
            </div>


            <x-slot:actions>

                <x-mary-button @click="$wire.modalPermissions = false" label="Cancel" />
                <x-mary-button label="Save" class="btn-primary" type="submit" spinner="savePermissionsToRole" />
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
        <x-mary-table :headers="$headers" :rows="$roles" :sort-by="$sortBy" with-pagination>

            @php
                $perPage = $roles->perPage();
                $currentPage = $roles->currentPage();
                $indexOffset = ($currentPage - 1) * $perPage;
            @endphp
            @scope('cell_counter', $role, $loop, $indexOffset)
                {{ $loop->index + 1 + $indexOffset }}
            @endscope

            @scope('cell_permissions', $role)
                <div class="flex flex-wrap items-center">
                    @foreach ($role->permissions as $permission)
                        <span class="m-1 badge badge-primary">{{ $permission->name }}</span>
                    @endforeach
                </div>
            @endscope


            @scope('actions', $role)
                <div class="flex items-center">
                    <x-mary-button spinner='getPermissions({{ $role->id }})' icon="o-key"
                        wire:click='getPermissions({{ $role->id }})' class="mr-1 btn-sm btn-secondary"></x-mary-button>
                    <x-mary-button spinner='update({{ $role->id }})' icon="o-pencil-square"
                        wire:click='update({{ $role->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                    <x-mary-button wire:click='delete({{ $role->id }})' wire:confirm='Are you sure to delete?'
                        spinner="delete({{ $role->id }})" icon="o-trash" class="btn-sm btn-warning"></x-mary-button>
                </div>
            @endscope


        </x-mary-table>
    </div>



</div>
