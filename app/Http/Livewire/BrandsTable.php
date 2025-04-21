<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Brand;
use App\Models\Shop_user;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class BrandsTable extends Component
{
    public $brand_id, $brand_name, $brand_status, $brand_image;
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $search = '';
    public $new_brand_name = '';
    public $new_brand_image = '';
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

    public function toggleStatus($brandId)
    {        
        $brand = Brand::find($brandId);
               
        if ($brand) {
            $brand->brand_status = $brand->brand_status === 1 ? 0 : 1;
            $brand->save();
        }
        
    }

    public function openAddModal()
    {
        $this->isEditMode = false;
        $this->reset(['new_brand_name']);
        $this->dispatchBrowserEvent('open-modal');
    }

    public function store()
    {
        $this->validate([
            'new_brand_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z]+(?: [A-Za-z]+)*(?: & [A-Za-z]+(?: [A-Za-z]+)*)?$/', 
            ],
            'new_brand_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'new_brand_name.required' => 'Brand name is required.',
            'new_brand_name.string' => 'Enter a valid brand name.',
            'new_brand_name.min' => 'Brand name must be at least 3 characters.',
            'new_brand_name.max' => 'Brand name cannot exceed 20 characters.',
            'new_brand_name.regex' => 'Only letters (upper and lowercase).',
            'new_brand_image.required' => 'Brand image is required.',
            'new_brand_image.image' => 'Uploaded file must be an image.',
            'new_brand_image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
            'new_brand_image.max' => 'Image size must be less than 2 MB.',
        ]);

        $shopId = session('selected_shop_id');
        
        $existingbrand = Brand::where('brand_name', $this->new_brand_name)
        ->where('shop_id', $shopId)
        ->first();

        if ($existingbrand) {
            session()->flash('error', 'Brand already exists.');
            return; 
        }

        $path = null;
        if($this->new_brand_image)
        {
            $filename = 'brand_'.uniqid().'.png';
            $img = Image::make($this->new_brand_image->getRealPath())->resize(600,800, function($constrain){
                $constrain->aspectRatio();
            });
            $path = 'goapp/images/brand/'.$filename;
            Storage::disk('s3')->put($path,(string)$img->encode());
        }
        Brand::create([
            'brand_name' => $this->new_brand_name,
            'shop_id' => $shopId,
            'brand_image' => $path,
        ]);

        session()->flash('message', 'Brand added successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function edit($brand_id)
    {
        $this->isEditMode = true;
        $this->brand_id = $brand_id;

        $brand = Brand::findOrFail($brand_id);
        $this->brand_name = $brand->brand_name;
        $this->existing_brand_image = $brand->brand_image;
        $this->dispatchBrowserEvent('open-modal');
    }

    public function update()
    {
        $this->validate([
            'brand_name' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z]+(?: [A-Za-z]+)*(?: & [A-Za-z]+(?: [A-Za-z]+)*)?$/', 
            ],
            'brand_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'brand_name.required' => 'Brand name is required.',
            'brand_name.string' => 'Enter a valid brand name.',
            'brand_name.min' => 'Brand name must be at least 3 characters.',
            'brand_name.max' => 'Brand name cannot exceed 20 characters.',
            'brand_name.regex' => 'Only letters (upper and lowercase).',
            'brand_image.image' => 'Uploaded file must be an image.',
            'brand_image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
            'brand_image.max' => 'Image size must be less than 2 MB.',
        ]);
        

        if (!$this->brand_id) {
            session()->flash('error', 'No Brand selected for update.');
            return;
        }
        $shopId = session('selected_shop_id');
        $existingbrand = Brand::where('brand_name', $this->brand_name)
            ->where('brand_id', '!=', $this->brand_id)
            ->where('shop_id', $shopId)
            ->first();

        if ($existingbrand) {
            session()->flash('error', 'Brand already exists.');
            return;
        }
        

        $brand = Brand::findOrFail($this->brand_id);
        $path = $brand->brand_image;

        if ($this->brand_image)
        {
            if ($path) {
                Storage::disk('s3')->delete($path);
            }

            $filename = 'brand_'.uniqid().'.png';
            $img = Image::make($this->brand_image->getRealPath())->resize(600,800, function($constrain){
                $constrain->aspectRatio();
            });
            $path = 'goapp/images/brand/'.$filename;
            Storage::disk('s3')->put($path,(string)$img->encode());
        }
        
        $brand->update([
            'brand_name' => $this->brand_name,
            'brand_image' => $path,
        ]);

        session()->flash('message', 'Brand updated successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }


    public function confirmDelete($brand_id)
    {
        $this->brand_id = $brand_id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $brand = Brand::find($this->brand_id);
    
        if ($brand) {
            $path = $brand->brand_image;
            Storage::disk('s3')->delete($path);
            $brand->delete();
            
            session()->flash('message', 'Brand deleted successfully.');
        } else {
            session()->flash('error', 'Brand not found.');
        }
    
        $this->dispatchBrowserEvent('close-delete-modal'); 
    }

    public function render()
    {
        $brands = Brand::where('shop_id', $this->selectedShopId)
            ->where('brand_name', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);
        return view('livewire.brands-table', compact('brands'));
    }
}
