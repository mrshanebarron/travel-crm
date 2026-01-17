<?php

namespace App\Livewire;

use App\Models\ClientNote;
use App\Models\Traveler;
use Livewire\Component;

class ClientNotes extends Component
{
    public Traveler $client;

    // Add note form
    public $showAddModal = false;
    public $noteType = 'note';
    public $noteContent = '';
    public $contactedAt = '';

    // Edit note form
    public $showEditModal = false;
    public $editNoteId = null;
    public $editNoteType = 'note';
    public $editNoteContent = '';
    public $editContactedAt = '';

    public function mount(Traveler $client)
    {
        $this->client = $client;
        $this->contactedAt = now()->format('Y-m-d\TH:i');
    }

    public function openAddModal()
    {
        $this->reset(['noteType', 'noteContent']);
        $this->noteType = 'note';
        $this->contactedAt = now()->format('Y-m-d\TH:i');
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function addNote()
    {
        $this->validate([
            'noteType' => 'required|in:note,call,email,meeting',
            'noteContent' => 'required|string',
            'contactedAt' => 'nullable|date',
        ]);

        $this->client->notes()->create([
            'type' => $this->noteType,
            'content' => $this->noteContent,
            'contacted_at' => $this->contactedAt ?: now(),
            'created_by' => auth()->id(),
        ]);

        $this->closeAddModal();
        $this->client->refresh();
    }

    public function openEditModal(ClientNote $note)
    {
        $this->editNoteId = $note->id;
        $this->editNoteType = $note->type;
        $this->editNoteContent = $note->content;
        $this->editContactedAt = $note->contacted_at->format('Y-m-d\TH:i');
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editNoteId = null;
    }

    public function updateNote()
    {
        $this->validate([
            'editNoteType' => 'required|in:note,call,email,meeting',
            'editNoteContent' => 'required|string',
            'editContactedAt' => 'nullable|date',
        ]);

        $note = ClientNote::findOrFail($this->editNoteId);
        $note->update([
            'type' => $this->editNoteType,
            'content' => $this->editNoteContent,
            'contacted_at' => $this->editContactedAt ?: now(),
        ]);

        $this->closeEditModal();
        $this->client->refresh();
    }

    public function deleteNote(ClientNote $note)
    {
        $note->delete();
        $this->client->refresh();
    }

    public function render()
    {
        return view('livewire.client-notes', [
            'notes' => $this->client->notes()->with('creator')->orderByDesc('contacted_at')->get(),
            'noteTypes' => ClientNote::TYPES,
        ]);
    }
}
