<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';

    public int $userId = 0;
    public bool $modalOpen = false;

    public $users = [];
    public $roles = [];

    public function mount()
    {
        $this->loadUsers();
        $this->roles = Role::pluck('name')->toArray();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')->latest()->get();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $user = User::findOrFail($id);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->roles->first()?->name ?? '';
            $this->password = '';
        } else {
            $this->resetFields();
        }

        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->userId = 0;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|string',
            'password' => $this->userId
                ? 'nullable|min:6'
                : 'required|min:6',
        ]);

        if ($this->userId) {

            $user = User::findOrFail($this->userId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);

            $user->syncRoles([$this->role]);

        } else {

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->role);
        }

        $this->closeModal();
        $this->loadUsers();
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        $user->delete();
        $this->loadUsers();
    }
};
?>

<div class="space-y-6 p-6">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <button wire:click="openModal"
        class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">
        Add User
    </button>

    <div class="bg-white shadow rounded">
        <div class="flex font-bold border-b p-2">
            <div class="w-3/12">Name</div>
            <div class="w-3/12">Email</div>
            <div class="w-2/12">Role</div>
            <div class="w-4/12">Actions</div>
        </div>

        @foreach ($users as $user)
            <div class="flex border-b p-2 items-center">
                <div class="w-3/12">{{ $user->name }}</div>
                <div class="w-3/12">{{ $user->email }}</div>
                <div class="w-2/12">
                    {{ $user->roles->pluck('name')->join(', ') }}
                </div>

                <div class="w-4/12 flex gap-2">
                    <button wire:click="openModal({{ $user->id }})"
                        class="px-3 py-1 bg-yellow-500 text-white rounded">
                        Edit
                    </button>

                    <button wire:click="deleteUser({{ $user->id }})"
                        class="px-3 py-1 bg-red-500 text-white rounded">
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded w-96">

                <h2 class="text-xl font-bold mb-4">
                    {{ $userId ? 'Edit User' : 'Add User' }}
                </h2>

                <input type="text" wire:model="name" class="border p-2 w-full mb-1" placeholder="Name">
@error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

<input type="email" wire:model="email" class="border p-2 w-full mb-1" placeholder="Email">
@error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

<input type="password" wire:model="password" class="border p-2 w-full mb-1" placeholder="Password">
@error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

<select wire:model="role" class="border p-2 w-full mb-1">
    <option value="">Select Role</option>
    @foreach($roles as $r)
        <option value="{{ $r }}">{{ $r }}</option>
    @endforeach
</select>
@error('role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded">
                        Cancel
                    </button>

                    <button wire:click="saveUser"
                        class="px-4 py-2 bg-blue-500 text-white rounded">
                        Save
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>