<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Manage Staff</h2>
        </div>
        <div class="col-12 col-md-6 text-end">
            <button class="btn btn-outline-danger" wire:click="openAddModal">
                <i class="ion-plus"></i> Add
            </button> 
        </div>
    </div>

    <div class="col-12">
        @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
    </div>

    <div class="row mb-3">
        <div class="col-3">
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search by user name..." />
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
                    <th style="width: 50%;">Name</th>
                    <th style="width: 50%;">Email</th>
                </tr>
                {{-- <tr>
                    <th style="width: 40%;">Name</th>
                    <th style="width: 40%;">Email</th>
                    <th style="width: 20%;">Actions</th>
                </tr> --}}
            </thead>
            <tbody>
                @forelse($mangeusers as $mangeuser)
                    <tr>
                        <td>{{ $mangeuser->name }}</td>
                        <td>{{ $mangeuser->email }}</td>
                        {{-- <td>
                            <button wire:click="editUser({{ $mangeuser->id }})" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Email
                            </button>                            
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No Staff found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $mangeusers->links() }}
        </div>

        <!-- Request/Add Modal -->
        <div class="modal modal-md" id="addRequestModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title">
                            {{ $isExistingUser ? 'Request User' : 'Add User' }}
                        </h5>
                        <button type="button" onclick="closeAddModal()" class="btn-close"></button>
                    </div>
        
                    @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
        
                    <div class="modal-body">
                        <form wire:submit.prevent="store">
                            
                            <!-- Name Field -->
                            @if (!$isExistingUser)
                            <div class="mb-3">
                                <label for="userName" class="form-label">Name</label>
                                <input type="text" id="userName" class="form-control" wire:model="name">
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif
                            
                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" class="form-control" wire:model="email">
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
        
                            <!-- Password Field -->
                            @if (!$isExistingUser)
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Password</label>
                                <input type="password" id="userPassword" class="form-control" wire:model="password">
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif
        
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    {{ $isExistingUser ? 'Request' : 'Add' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
        
        <!-- Edit Email Modal -->
        {{-- <div class="modal modal-md" id="editModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title">Edit User Email</h5>
                        <button type="button" onclick="closeEditModal()" class="btn-close"></button>
                    </div>
        
                    <div class="modal-body">
                        <form wire:submit.prevent="updateUser">
                            <div class="mb-3">
                                <label for="email" class="form-label">New Email</label>
                                <input type="email" id="email" class="form-control" wire:model="email">
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
        
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</div>

<script>
    // Add Modal Handlers
    document.addEventListener('open-add-modal', () => {
        const addModal = document.getElementById('addRequestModal');
        addModal.style.display = 'block';
    });

    document.addEventListener('close-add-modal', () => {
        const addModal = document.getElementById('addRequestModal');
        addModal.style.display = 'none';
    });

    function closeAddModal() {
        document.dispatchEvent(new Event('close-add-modal'));
    }

    // Edit Modal Handlers
    document.addEventListener('open-edit-modal', () => {
        const editModal = document.getElementById('editModal');
        editModal.style.display = 'block';
    });

    document.addEventListener('close-edit-modal', () => {
        const editModal = document.getElementById('editModal');
        editModal.style.display = 'none';
    });

    function closeEditModal() {
        document.dispatchEvent(new Event('close-edit-modal'));
    }
</script>

