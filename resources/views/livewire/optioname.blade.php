<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Option Name's</h2>
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
                    placeholder="Search by name..." />
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
                    <th style="width: 50%;">Names</th>
                    <th style="width: 50%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($optionames as $optioname)
                    <tr>
                        <td>{{ $optioname->option_name }}</td>
                        <td>
                            <button wire:click="edit({{ $optioname->option_id }})" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No Optioname found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $optionames->links() }}
        </div>
        <!-- Edit/Add Modal -->
        <div class="modal modal-md" id="editAddModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title" id="modalLabel">
                            {{ $isEditMode ? 'Edit Optioname' : 'Add Optioname' }}
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
                                <label for="optionameName" class="form-label">Name</label>
                                <input 
                                    type="text" 
                                    id="optionameName" 
                                    class="form-control" 
                                    wire:model="{{ $isEditMode ? 'option_name' : 'new_option_name' }}">
                                @error($isEditMode ? 'option_name' : 'new_option_name') 
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
                     
    </div>   
</div>