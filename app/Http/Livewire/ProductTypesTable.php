<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product_type;
use App\Models\Shop_user;

class ProductTypesTable extends Component
{
    public $product_type_id, $product_type_name, $product_type_status;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $search = '';
    public $new_product_type_name = '';
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

    public function toggleStatus($productTypeId)
    {        
        $productType = Product_type::find($productTypeId);
               
        if ($productType) {
            $productType->product_type_status = $productType->product_type_status === 1 ? 0 : 1;
            $productType->save();
        }
        
    }

    public function openAddModal()
    {
        $this->isEditMode = false;
        $this->reset(['new_product_type_name']);
        $this->dispatchBrowserEvent('open-modal');
    }

    public function store()
    {
        $this->validate([
            'new_product_type_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9][A-Za-z0-9\s-]*$/', 
            ],
        ], [
            'new_product_type_name.required' => 'Product type name is required.',
            'new_product_type_name.string' => 'Enter a valid product type name.',
            'new_product_type_name.min' => 'Product type name must be at least 3 characters.',
            'new_product_type_name.max' => 'Product type name cannot exceed 20 characters.',
            'new_product_type_name.regex' => 'Only numbers, letters (upper and lowercase).',
        ]);

        $shopId = session('selected_shop_id');

        $existingProductType = Product_type::where('product_type_name', $this->new_product_type_name)
        ->where('shop_id', $shopId)
        ->first();

        if ($existingProductType) {
            session()->flash('error', 'Product Type already exists.');
            return; 
        }

        Product_type::create([
            'product_type_name' => $this->new_product_type_name,
            'shop_id' => $shopId,
        ]);

        session()->flash('message', 'Product Type added successfully.');

        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function edit($product_type_id)
    {
        $this->isEditMode = true;
        $this->product_type_id = $product_type_id; 

        $productType = Product_type::findOrFail($product_type_id);
        $this->product_type_name = $productType->product_type_name;
        $this->dispatchBrowserEvent('open-modal');
    }

    public function update()
    {
        $this->validate([
            'product_type_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9][A-Za-z0-9\s-]*$/',
            ],
        ], [
            'product_type_name.required' => 'Product type name is required.',
            'product_type_name.string' => 'Enter a valid product type name.',
            'product_type_name.min' => 'Product type name must be at least 3 characters.',
            'product_type_name.max' => 'Product type name cannot exceed 20 characters.',
            'product_type_name.regex' => 'Only numbers, letters (upper and lowercase).',
        ]);

        if (!$this->product_type_id) {
            session()->flash('error', 'No Product Type selected for update.');
            return;
        }

        $shopId = session('selected_shop_id');
        $existingProductType = Product_type::where('product_type_name', $this->product_type_name)
            ->where('product_type_id', '!=', $this->product_type_id)
            ->where('shop_id', $shopId)
            ->first();

        if ($existingProductType) {
            session()->flash('error', 'Product Type already exists.');
            return;
        }

        $productType = Product_type::findOrFail($this->product_type_id);
        $productType->update([
            'product_type_name' => $this->product_type_name,
        ]);

        session()->flash('message', 'Product Type updated successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function confirmDelete($product_type_id)
    {
        $this->product_type_id = $product_type_id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $productType = Product_type::find($this->product_type_id);
    
        if ($productType) {
            $productType->delete();
            session()->flash('message', 'Product type deleted successfully.');
        } else {
            session()->flash('error', 'Product type not found.');
        }
    
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function render()
    {
        $productTypes = Product_type::where('shop_id', $this->selectedShopId)
        ->where('product_type_name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage);

        return view('livewire.product-types-table', compact('productTypes'));
    }
}
