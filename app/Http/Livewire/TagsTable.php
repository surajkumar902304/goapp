<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tag;
use App\Models\Shop_user;

class TagsTable extends Component
{
    public $tag_id, $tag_name, $tag_status;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $search = '';
    public $new_tag_name = '';
    public $isEditMode = false;

    public $selectedShopId;
    public function mount()
    {
        $this->selectedShopId = session('selected_shop_id');
    }

    public function updatedPerPage()
    {
        $this->resetPage(); 
    }

    public function toggleStatus($tagId)
    {        
        $tag = Tag::find($tagId);
        if ($tag) {
            $tag->tag_status = $tag->tag_status === 1 ? 0 : 1;
            $tag->save();
        }
    }

    public function openAddModal()
    {
        $this->isEditMode = false;
        $this->reset(['new_tag_name']);
        $this->dispatchBrowserEvent('open-modal');
    }


    public function store()
    {
        $this->validate([
            'new_tag_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9]+(?:([A-Za-z0-9_\-\/]+|\s)[A-Za-z0-9]+)*$/'
            ],
        ], [
            'new_tag_name.required' => 'Tag name is required.',
            'new_tag_name.string' => 'Enter a valid tag name.',
            'new_tag_name.min' => 'Tag name must be at least 3 characters.',
            'new_tag_name.max' => 'Tag name cannot exceed 20 characters.',
            'new_tag_name.regex' => 'Only letters (upper and lowercase).',
        ]);

        $shopId = session('selected_shop_id');
        
        $existingtag = Tag::where('tag_name', $this->new_tag_name)
        ->where('shop_id', $shopId)
        ->first();

        if ($existingtag) {
            session()->flash('error', 'Tag already exists.');
            return; 
        }

        Tag::create([
            'tag_name' => $this->new_tag_name,
            'shop_id' => $shopId,
        ]);

        session()->flash('message', 'Tag added successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function edit($tag_id)
    {
        $this->isEditMode = true;
        $this->tag_id = $tag_id; 

        $tag = Tag::findOrFail($tag_id);
        $this->tag_name = $tag->tag_name;
        $this->dispatchBrowserEvent('open-modal');
    }

    public function update()
    {
        $this->validate([
            'tag_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9]+(?:([A-Za-z0-9_\-\/]+|\s)[A-Za-z0-9]+)*$/'
            ],
        ], [
            'tag_name.required' => 'Tag name is required.',
            'tag_name.string' => 'Enter a valid tag name.',
            'tag_name.min' => 'Tag name must be at least 3 characters.',
            'tag_name.max' => 'Tag name cannot exceed 20 characters.',
            'tag_name.regex' => 'Only letters (upper and lowercase).',
        ]);

        if (!$this->tag_id) {
            session()->flash('error', 'No Tag selected for update.');
            return;
        }

        $shopId = session('selected_shop_id');
        $existingtag = Tag::where('tag_name', $this->tag_name)
            ->where('tag_id', '!=', $this->tag_id)
            ->where('shop_id', $shopId)
            ->first();

        if ($existingtag) {
            session()->flash('error', 'Tag already exists.');
            return;
        }

        $tag = tag::findOrFail($this->tag_id);
        $tag->update([
            'tag_name' => $this->tag_name,
        ]);

        session()->flash('message', 'Tag updated successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }


    public function confirmDelete($tag_id)
    {
        $this->tag_id = $tag_id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $tag = Tag::find($this->tag_id);
        if ($tag) {
            $tag->delete();
            session()->flash('message', 'Tag deleted successfully.');
        } else {
            session()->flash('error', 'Tag not found.');
        }
        $this->dispatchBrowserEvent('close-delete-modal');
    }


    public function render()
    {
        $tags = Tag::where('shop_id', $this->selectedShopId)
            ->where('tag_name', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.tags-table', compact('tags'));
    }
}
