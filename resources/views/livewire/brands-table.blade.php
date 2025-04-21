<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Brand's</h2>
        </div>
        <div class="col-12 col-md-6 text-end">
            <button class="btn btn-outline-danger" wire:click="openAddModal">
                <i class="ion-plus"></i> Add
            </button> 
        </div>
        <div class="col-12">
            @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-3">
                <input type="text" wire:model.debounce.300ms="search" class="form-control" 
                    placeholder="Search by brand name..." />
        </div>
        <div class="col-9 d-flex justify-content-end align-items-center">
            <div class="me-2"><label for="perPage" class="mr-2">Rows per page:</label></div>
            <div>
                <select wire:model="perPage" id="perPage" class="form-select w-auto">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div> 
        </div>
    </div>
    

    <div class="table-responsive flex-grow-1">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th style="width: 30%;">Image</th>
                    <th style="width: 30%;">Brands</th>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 20%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td>
                            <img src="{{ config('app.cdn_url').$brand->brand_image }}" alt="{{ $brand->brand_name }}" width="50px" height="50px">
                        </td>
                        <td>{{ $brand->brand_name }}</td>
                        <td>
                            <button wire:click="toggleStatus({{ $brand->brand_id }})" class="btn btn-sm 
                                {{ $brand->brand_status === 1 ? 'btn-success' : 'btn-secondary' }}">
                                <i class="fa-solid fa-toggle-{{ $brand->brand_status === 1 ? 'on' : 'off' }}"></i>
                            </button>
                        </td>
                        <td>
                            <button wire:click="edit({{ $brand->brand_id }})" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $brand->brand_id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No Brands found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $brands->links() }}
        </div>
        <!-- Edit/Add Modal -->
        <div class="modal modal-md" id="editAddModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title" id="modalLabel">
                            {{ $isEditMode ? 'Edit Brand' : 'Add Brand' }}
                        </h5>
                        <button type="button" onclick="closeModal()" class="btn-close"></button>
                    </div>
                    
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
        
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
        
                            <div class="mb-3">
                                <label for="brandName" class="form-label">Name</label>
                                <input 
                                    type="text" 
                                    id="brandName" 
                                    class="form-control" 
                                    wire:model="{{ $isEditMode ? 'brand_name' : 'new_brand_name' }}">
                                @error($isEditMode ? 'brand_name' : 'new_brand_name') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
        
                            <div class="mb-3">
                                <label for="productImage" class="form-label">Image</label>
                                <input 
                                    type="file" 
                                    id="productImage" 
                                    class="form-control" 
                                    wire:model="{{ $isEditMode ? 'brand_image' : 'new_brand_image' }}">
        
                                @error($isEditMode ? 'brand_image' : 'new_brand_image') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
        
                            @if ($isEditMode && $existing_brand_image)
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <img src="{{ Storage::disk('s3')->url($existing_brand_image) }}" alt="{{ $brand_name }}" width="100px" height="100px">
                                </div>
                            @endif
        
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    {{ $isEditMode ? 'Update' : 'Add' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Confirmation Modal -->
        <div class="modal modal-sm" id="deleteConfirmationModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title">Delete Brand</h5>
                        <button type="button" onclick="closeDeleteModal()" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this Brand?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>               
    </div>    
</div>