<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Shop's</h2>
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
                    placeholder="Search by shop name..." />
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
                    <th style="width: 25%;">Shops</th>
                    <th style="width: 25%;">User</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 40%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                    <tr>
                        <td>{{ $shop->shop_name }}</td>
                        <td>
                            @if($shop->users->isNotEmpty())
                                @foreach($shop->users as $user)
                                    {{ $user->name }}
                                    @if (!$loop->last), @endif
                                @endforeach
                            @else
                                <span style="color: red">Not selected</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="toggleStatus({{ $shop->shop_id }})" class="btn btn-sm 
                                {{ $shop->shop_status === 1 ? 'btn-success' : 'btn-secondary' }}">
                                <i class="fa-solid fa-toggle-{{ $shop->shop_status === 1 ? 'on' : 'off' }}"></i>
                            </button>
                        </td>
                        <td>
                            <button wire:click="edit({{ $shop->shop_id }})" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            @if($shop->users->isEmpty())
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#userAddModal" wire:click="prepareModal({{ $shop->shop_id }})">
                                    Add Existing Owner
                                </button> 
                                <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#userCreateModal" wire:click="prepareModal({{ $shop->shop_id }})">
                                    Create New Owner
                                </button>  
                            @endif                         
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No Shops found.</td>
                    </tr>
                @endforelse
            </tbody>            
        </table>        
        <div class="d-flex justify-content-center mt-3">
            {{ $shops->links() }}
        </div>

        <!-- Edit/Add Modal -->
        <div class="modal modal-md" id="editAddModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title" id="modalLabel">
                            {{ $isEditMode ? 'Edit Shop' : 'Add Shop' }}
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
                                <label for="shopName" class="form-label">Name</label>
                                <input 
                                    type="text" 
                                    id="shopName" 
                                    class="form-control" 
                                    wire:model="{{ $isEditMode ? 'shop_name' : 'new_shop_name' }}">
                                @error($isEditMode ? 'shop_name' : 'new_shop_name') 
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
        <!-- Add Owner Modal -->
        <div class="modal fade" id="userAddModal" wire:ignore.self tabindex="-1" aria-labelledby="userAddModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userAddModalLabel">Add Owner to Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="addUserToShop">
                        <div class="modal-body">
                            <input type="hidden" wire:model="selectedShopId">
                            <div class="mb-3">
                                <label for="userSelect" class="form-label">Select User</label>
                                <select class="form-select" id="userSelect" wire:model="selectedUserId">
                                    <option value="">Choose a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedUserId') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="shopName" class="form-label">Selected Shop</label>
                                <input type="text" id="shopName" class="form-control" value="{{ $shops->firstWhere('shop_id', $selectedShopId)->shop_name ?? '' }}" disabled>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Owner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>  
        <!-- Create New Owner Modal -->
        <div class="modal fade" id="userCreateModal" tabindex="-1" aria-labelledby="userCreateModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="userCreateModalLabel">Add Owner to Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form wire:submit.prevent="createUserToShop">
                            <div class="mb-3">
                                <label for="userName" class="form-label">Name</label>
                                <input type="text" id="userName" class="form-control" wire:model="name" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" class="form-control" wire:model="email" required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Password</label>
                                <input type="password" id="userPassword" class="form-control" wire:model="password" required>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- Shop Name (Read-only) -->
                            <div class="mb-3">
                                <label for="shopName" class="form-label">Selected Shop</label>
                                <input type="text" id="shopName" class="form-control" value="{{ $shops->firstWhere('shop_id', $selectedShopId)->shop_name ?? '' }}" disabled>
                            </div>
                        
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
          
    </div>   
</div>

<script>
    document.addEventListener('close-modal', () => {
    const modal = document.getElementById('userAddModal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    bsModal.hide();
});

document.addEventListener('close-modal', () => {
    const modal = document.getElementById('userCreateModal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    bsModal.hide();
});

</script>