<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Tag's</h2>
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
                    placeholder="Search by tag name..." />
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
                    <th style="width: 50%;">Tags</th>
                    <th style="width: 25%;">Status</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $tag)
                    <tr>
                        <td>{{ $tag->tag_name }}</td>
                        <td>
                            <button wire:click="toggleStatus({{ $tag->tag_id }})" class="btn btn-sm 
                                {{ $tag->tag_status === 1 ? 'btn-success' : 'btn-secondary' }}">
                                <i class="fa-solid fa-toggle-{{ $tag->tag_status === 1 ? 'on' : 'off' }}"></i>
                            </button>
                        </td>
                        <td>
                            <button wire:click="edit({{ $tag->tag_id }})" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $tag->tag_id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No tags found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $tags->links() }}
        </div>
        <!-- Edit/Add Modal -->
        <div class="modal modal-md" id="editAddModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title" id="modalLabel">
                            {{ $isEditMode ? 'Edit Tag' : 'Add Tag' }}
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
                                <label for="productTypeName" class="form-label">Name</label>
                                <input 
                                    type="text" 
                                    id="productTypeName" 
                                    class="form-control" 
                                    wire:model="{{ $isEditMode ? 'tag_name' : 'new_tag_name' }}">
                                @error($isEditMode ? 'tag_name' : 'new_tag_name') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                            
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
                        <h5 class="modal-title">Delete Tag</h5>
                        <button type="button" onclick="closeDeleteModal()" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this Tag?</p>
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