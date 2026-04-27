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
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
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

            $user->syncRoles([$this->role]);
        }

        $this->closeModal();
        $this->loadUsers();
    }

    public function deleteUser($id)
    {
        if (!auth()->user()->hasRole('super admin')) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        $this->loadUsers();
    }
};
?>

<div class="space-y-6 p-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-4">Users</h1>

    {{-- Add Button --}}
    <button wire:click="openModal"
        class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        Add User
    </button>

    <div class="bg-white shadow rounded overflow-hidden">

        <div class="flex font-semibold border-b bg-gray-50 p-3 text-gray-700">
            <div class="w-3/12">Name</div>
            <div class="w-3/12">Email</div>
            <div class="w-2/12">Role</div>
            <div class="w-4/12">Actions</div>
        </div>

        @foreach ($users as $user)
            <div class="flex border-b p-3 items-center hover:bg-gray-50">

                <div class="w-3/12 text-gray-800">{{ $user->name }}</div>
                <div class="w-3/12 text-gray-600">{{ $user->email }}</div>

                <div class="w-2/12">
                    <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700">
                        {{ $user->roles->pluck('name')->join(', ') }}
                    </span>
                </div>

                <div class="w-4/12 flex gap-2">

                    <button wire:click="openModal({{ $user->id }})"
                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                        Edit
                    </button>

                    @if(auth()->user()?->hasRole('super admin'))
                        <button wire:click="deleteUser({{ $user->id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                            Delete
                        </button>
                    @endif

                </div>
            </div>
        @endforeach

    </div>

    {{-- Modal --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">

            <div class="bg-white p-6 rounded w-96 shadow-lg">

                <h2 class="text-xl font-bold mb-4">
                    {{ $userId ? 'Edit User' : 'Add User' }}
                </h2>

                <input type="text" wire:model="name"
                    class="border p-2 w-full mb-2 rounded"
                    placeholder="Name">

                <input type="email" wire:model="email"
                    class="border p-2 w-full mb-2 rounded"
                    placeholder="Email">

                {{-- Password --}}
                <div x-data="{ show: false }" class="relative mb-2">
                    <input :type="show ? 'text' : 'password'"
                        wire:model="password"
                        class="border p-2 w-full rounded pr-14"
                        placeholder="Password">

                    <button type="button"
                        @click="show = !show"
                        class="absolute right-2 top-2 text-sm text-gray-600">
                        <span x-text="show ? 'Hide' : 'Show'"></span>
                    </button>
                </div>

                <select wire:model="role"
                    class="border p-2 w-full mb-4 rounded">
                    <option value="">Select Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-2">

                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>

                    <button wire:click="saveUser"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>

                </div>

            </div>
        </div>
    @endif

</div>