<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Option_name;

class Optioname extends Component
{
    public $option_id, $option_name;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $search = '';
    public $new_option_name = '';
    public $isEditMode = false;

    public function updatedPerPage()
    {
        $this->resetPage(); 
    }

    public function openAddModal()
    {
        $this->isEditMode = false;
        $this->reset(['new_option_name']);
        $this->dispatchBrowserEvent('open-modal');
    }
    public function store()
    {
        $this->validate([
            'new_option_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9][A-Za-z0-9\s]*$/', 
            ],
        ], [
            'new_option_name.required' => 'Option name is required.',
            'new_option_name.string' => 'Enter a valid Option name.',
            'new_option_name.min' => 'Option name must be at least 3 characters.',
            'new_option_name.max' => 'Option name cannot exceed 20 characters.',
            'new_option_name.regex' => 'Only numbers, letters (upper and lowercase).',
        ]);

        $existingOptioname = Option_name::where('option_name', $this->new_option_name)
        ->first();

        if ($existingOptioname) {
            session()->flash('error', 'Option name already exists.');
            return; 
        }

        Option_name::create([
            'option_name' => $this->new_option_name,
        ]);

        session()->flash('message', 'Option name added successfully.');

        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function edit($option_id)
    {
        $this->isEditMode = true;
        $this->option_id = $option_id; 

        $Optioname = Option_name::findOrFail($option_id);
        $this->option_name = $Optioname->option_name;
        $this->dispatchBrowserEvent('open-modal');
    }

    public function update()
    {
        $this->validate([
            'option_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9][A-Za-z0-9\s]*$/',
            ],
        ], [
            'option_name.required' => 'Option name is required.',
            'option_name.string' => 'Enter a valid Option name.',
            'option_name.min' => 'Option name must be at least 3 characters.',
            'option_name.max' => 'Option name cannot exceed 20 characters.',
            'option_name.regex' => 'Only numbers, letters (upper and lowercase).',
        ]);

        if (!$this->option_id) {
            session()->flash('error', 'No Option name selected for update.');
            return;
        }

        $existingOptioname = Option_name::where('option_name', $this->option_name)
            ->where('option_id', '!=', $this->option_id)
            ->first();

        if ($existingOptioname) {
            session()->flash('error', 'Option name already exists.');
            return;
        }

        $Optioname = Option_name::findOrFail($this->option_id);
        $Optioname->update([
            'option_name' => $this->option_name,
        ]);

        session()->flash('message', 'Option name updated successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }
    public function render()
    {
        $optionames = Option_name::where('option_name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage);
        return view('livewire.optioname', compact('optionames'));
    }
}
