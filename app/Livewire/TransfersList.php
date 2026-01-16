<?php

namespace App\Livewire;

use App\Models\Transfer;
use Livewire\Component;
use Livewire\WithPagination;

class TransfersList extends Component
{
    use WithPagination;

    public $status = '';

    protected $queryString = ['status'];

    public function mount()
    {
        $this->status = request('status', '');
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function render()
    {
        $query = Transfer::with('expenses')
            ->orderBy('request_date', 'desc');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.transfers-list', [
            'transfers' => $query->paginate(25),
        ]);
    }
}
