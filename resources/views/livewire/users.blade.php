<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Office;

new class extends Component {
    use WithPagination;
    use Toast;

    public $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $search = '';
    public $transaction = 'add';
    public $modalUser = false;
    public $modalRoles = false;
    public $selectedUser;
    public $selectedRoles = [];

    public $name;
    public $email;
    public $password;

    public $position;

    public $officeDropdowns = [];
    public $selectedOffices = [];

    public $userOfficeId;

    public function mount()
    {
        // Initialize with top-level offices
        $this->officeDropdowns[0] = Office::whereNull('parent_id')
            ->get()
            ->map(function ($office) {
                return [
                    'id' => $office->id,
                    'name' => $office->name,
                ];
            })
            ->toArray();
        // add "Select Office" to the dropdown
        array_unshift($this->officeDropdowns[0], ['id' => '', 'name' => 'Select Office']);
    }

    public function updatedSelectedOffices($value, $key)
    {
        $level = $key + 1;

        // Clear out lower-level selections
        $this->selectedOffices = array_slice($this->selectedOffices, 0, $key + 1);
        $this->officeDropdowns = array_slice($this->officeDropdowns, 0, $level);

        if ($value) {
            // Load child offices for the selected office
            $this->officeDropdowns[$level] = Office::where('parent_id', $value)
                ->get()
                ->map(function ($office) {
                    return [
                        'id' => $office->id,
                        'name' => $office->name,
                    ];
                })
                ->toArray();
            // add "Select Office" to the dropdown
            array_unshift($this->officeDropdowns[$level], ['id' => '', 'name' => 'N/A']);
        }
    }

    public function with(): array
    {
        $table_headers = [['key' => 'counter', 'label' => '#', 'sortable' => false], ['key' => 'name', 'label' => 'Name'], ['key' => 'email', 'label' => 'Email'], ['key' => 'office_name', 'label' => 'Office'], ['key' => 'roles', 'label' => 'Roles', 'sortable' => false], ['key' => 'position', 'label' => 'Position'], ['key' => 'actions', 'label' => '', 'sortable' => false]];
        $allRoles = Role::orderBy('name')->get();
        return [
            'users' => $this->getUsers(),
            'headers' => $table_headers,
            'allRoles' => $allRoles,
        ];
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email' . ($this->transaction == 'edit' ? ',' . $this->selectedUser->id : ''),
            'position' => 'nullable',
        ];

        // Additional rules for 'add' transaction
        if ($this->transaction == 'add') {
            $rules['password'] = 'required';
        }

        $this->validate($rules);

        // Data to be saved
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'position' => $this->position,
        ];

        // Add password if provided
        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if (end($this->selectedOffices)) {
            $data['office_id'] = end($this->selectedOffices);
        }

        // Create or update user
        if ($this->transaction == 'add') {
            User::create($data);
        } else {
            $user = User::find($this->selectedUser->id);
            $user->update($data);
        }

        $this->success('User ' . ($this->transaction == 'add' ? 'created' : 'updated') . ' successfully');
        $this->modalUser = false;
        $this->resetExcept(['officeDropdowns']);
    }

    public function addUser()
    {
        $this->transaction = 'add';
        $this->modalUser = true;
    }

    public function getUsers()
    {
        $query = User::with('office')->select('users.*', 'offices.name as office_name')->leftjoin('offices', 'offices.id', '=', 'users.office_id')->orderBy(...array_values($this->sortBy));

        // add the search
        if ($this->search) {
            // $query = $query->where('name', 'like', '%' . $this->search . '%')
            // orWhere('email', 'like', '%' . $this->search . '%');
            $query = $query->whereAny(['name', 'email'], 'like', "%$this->search%");
        }

        $query = $query->paginate(10);
        return $query;
    }

    public function delete(User $user)
    {
        $user->delete();
        $this->success('User deleted successfully');
    }

    public function update(User $user)
    {
        $this->selectedUser = $user;
        $this->transaction = 'edit';
        $this->name = $user->name;
        $this->email = $user->email;
        $this->position = $user->position;
        $this->password = '';
        $this->modalUser = true;
    }

    public function getRoles($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->selectedRoles = $this->selectedUser->roles->pluck('name')->toArray();
        $this->modalRoles = true;
    }

    public function saveRolesToUser()
    {
        $this->selectedUser->syncRoles($this->selectedRoles);
        $this->success('Roles saved successfully');
        $this->modalRoles = false;
        $this->selectedRoles = [];
    }
};
?>

<div class="p-4 bg-base-100">
    <div class="flex items-center justify-between mb-2">
        <x-mary-button label="Create User" wire:click='addUser' icon="o-plus" class="btn-primary" />
        <x-mary-input wire:model.live='search' icon="o-magnifying-glass" clearable />
    </div>

    <x-mary-modal wire:model="modalUser" title="{{ $transaction == 'add' ? 'Add' : 'Edit' }} User"
        subtitle="{{ $transaction == 'add' ? 'Add new' : 'Edit existing' }} User" separator box-class="" persistent>

        <x-mary-form wire:submit="saveUser">
            <x-mary-input icon="o-user" label="Name" wire:model="name" />


            <x-mary-input icon="o-envelope" label="Email" wire:model="email" />

            <x-mary-input icon="o-list-bullet" label="Position" wire:model="position" />
            <x-mary-input icon="o-eye" type="password" label="Password" wire:model="password" />

            {{-- for the office --}}
            @foreach ($officeDropdowns as $index => $dropdown)
                @if (count($dropdown) > 1)
                    <x-mary-select label="{{ $index == 0 ? 'Office' : 'Sub Office' }}" :options="$dropdown"
                        wire:model.live="selectedOffices.{{ $index }}" />
                @endif
            @endforeach


            <x-slot:actions>

                <x-mary-button @click="$wire.modalUser = false" label="Cancel" />
                <x-mary-button label="{{ $transaction == 'add' ? 'Add' : 'Edit' }} User" class="btn-primary"
                    type="submit" spinner="saveUser, selectedOffices" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>

    {{-- MODAL ADD USER --}}
    <x-mary-modal wire:model="modaluser" title="{{ $selectedRole->name ?? '' }}"
        subtitle="{{ $selectedRole->description ?? '' }}" separator box-class="max-w-2xl" persistent>

        <x-mary-form wire:submit="savePermissionsToRole">
            <div class="grid grid-cols-3 gap-4">
                @foreach ($allRoles as $role)
                    <x-mary-checkbox wire:model="selectedPermissions" label="{{ $role->name }} "
                        value="{{ $role->name }}" />
                @endforeach
            </div>

            <x-slot:actions>

                <x-mary-button @click="$wire.modalPermissions = false" label="Cancel" />
                <x-mary-button label="Save" class="btn-primary" type="submit" spinner="savePermissionsToRole" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>


    {{-- MODAL ROLES --}}
    <x-mary-modal wire:model="modalRoles" title="{{ $selectedUser->name ?? '' }}"
        subtitle="{{ $selectedUser->name ?? '' }}" separator box-class="max-w-2xl" persistent>

        <x-mary-form wire:submit="saveRolesToUser">
            <div class="max-w-full">
                <table class="table">
                    <tr>
                        <th>Roles</th>
                        <th>Permissions</th>
                    </tr>
                    @foreach ($allRoles as $role)
                        {{-- <x-mary-checkbox wire:model="selectedRoles" label="{{ $role->name }} "
                        value="{{ $role->name }}" /> --}}
                        <tr>
                            <td>
                                <x-mary-checkbox wire:model="selectedRoles" label="{{ $role->name }} "
                                    value="{{ $role->name }}" />
                            </td>
                            <td>
                                @foreach ($role->permissions as $permission)
                                    {{-- {{ $permission->name }} --}}
                                    {{-- display permissions in badges --}}
                                    <span class="badge badge-primary">{{ $permission->name }}</span>
                                @endforeach
                            </td>
                    @endforeach
                </table>
            </div>


            <x-slot:actions>

                <x-mary-button @click="$wire.modalRoles = false" label="Cancel" />
                <x-mary-button label="Save" class="btn-primary" type="submit" spinner="saveRolesToUser" />
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
        <x-mary-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>

            @php
                $perPage = $users->perPage();
                $currentPage = $users->currentPage();
                $indexOffset = ($currentPage - 1) * $perPage;
            @endphp
            @scope('cell_counter', $user, $loop, $indexOffset)
                {{ $loop->index + 1 + $indexOffset }}
            @endscope

            @scope('cell_roles', $user)
                <div class="flex flex-wrap items-center">
                    @foreach ($user->roles as $role)
                        <span class="m-1 badge badge-primary whitespace-nowrap">{{ $role->name }}</span>
                    @endforeach
                </div>
            @endscope

            @scope('cell_office_name', $user)
                <div class="flex flex-wrap items-center">
                    @if ($user->office)
                        @foreach ($user->office->getHierarchy() as $office)
                            @if (!$loop->first)
                                {{-- add arrow --}}
                                <x-mary-icon name="o-arrow-right" class="m-1" />
                            @endif
                            <span class="m-1 badge badge-primary whitespace-nowrap">{{ $office->name }}</span>
                        @endforeach
                    @endif
                </div>
            @endscope



            {{-- actions --}}
            @scope('actions', $user)
                <div class="flex items-center">
                    <x-mary-button spinner='getRoles({{ $user->id }})' icon="o-key"
                        wire:click='getRoles({{ $user->id }})' class="mr-1 btn-sm btn-secondary"></x-mary-button>
                    <x-mary-button spinner='update({{ $user->id }})' icon="o-pencil-square"
                        wire:click='update({{ $user->id }})' class="mr-1 btn-sm btn-primary"></x-mary-button>
                    <x-mary-button wire:click='delete({{ $user->id }})' wire:confirm='Are you sure to delete?'
                        spinner="delete({{ $user->id }})" icon="o-trash" class="btn-sm btn-warning"></x-mary-button>
                </div>
            @endscope


        </x-mary-table>
    </div>



</div>
