<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UsersList extends Component
{
    // Add user form
    public $showAddModal = false;
    public $addName = '';
    public $addEmail = '';
    public $addPassword = '';
    public $addPasswordConfirmation = '';
    public $addRole = 'user';

    // Edit user form
    public $showEditModal = false;
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editPassword = '';
    public $editPasswordConfirmation = '';
    public $editRole = 'user';

    public function mount()
    {
        // Only super_admin can manage users
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }

    public function openAddModal()
    {
        $this->reset(['addName', 'addEmail', 'addPassword', 'addPasswordConfirmation']);
        $this->addRole = 'user';
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetValidation();
    }

    public function createUser()
    {
        $this->validate([
            'addName' => ['required', 'string', 'max:255'],
            'addEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'addPassword' => ['required', 'min:8'],
            'addPasswordConfirmation' => ['required', 'same:addPassword'],
            'addRole' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $this->addName,
            'email' => $this->addEmail,
            'password' => Hash::make($this->addPassword),
        ]);

        $user->assignRole($this->addRole);

        $this->closeAddModal();
        session()->flash('success', 'User created successfully.');
    }

    public function openEditModal(User $user)
    {
        $this->editUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->roles->first()?->name ?? 'user';
        $this->editPassword = '';
        $this->editPasswordConfirmation = '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editUserId = null;
        $this->resetValidation();
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->editUserId],
            'editRole' => ['required', 'exists:roles,name'],
            'editPassword' => ['nullable', 'min:8'],
            'editPasswordConfirmation' => ['nullable', 'same:editPassword'],
        ]);

        $user = User::findOrFail($this->editUserId);

        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
        ]);

        if (!empty($this->editPassword)) {
            $user->update(['password' => Hash::make($this->editPassword)]);
        }

        $user->syncRoles([$this->editRole]);

        $this->closeEditModal();
        session()->flash('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function render()
    {
        return view('livewire.users-list', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
