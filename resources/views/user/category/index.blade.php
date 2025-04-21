@extends('layouts.user')

@section('title', 'Categories')

@section('content')

    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Categories</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('category.add') }}" class="btn btn-outline-danger">Add Category</a>
        </div>
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <form action="{{ route('category.index') }}" method="GET" class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="pe-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Search by category title..." 
                        value="{{ request()->get('search') }}" 
                        onkeypress="disableEnter(event)"
                    />
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <label for="perPage" class="me-2">Rows per page:</label>
                <select name="perPage" id="perPage" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="5" {{ request()->get('perPage', 10) == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request()->get('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request()->get('perPage', 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request()->get('perPage', 10) == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
        </form>
    </div>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Type</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cats as $cat)
                <tr>
                    <td>
                        <img src="{{ config('app.cdn_url').$cat->cat_image }}" alt="{{ $cat->cat_title }}" width="50px" height="50px">
                    </td>
                    <td>{{ $cat->cat_title }}</td>
                    <td>{{ $cat->cat_type }}</td>
                    <td>
                        <!-- Example: If you want to see matched products -->
                        <a href="{{ route('category.view', ['cat_id' => $cat->cat_id]) }}" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('category.edit', ['cat_id' => $cat->cat_id]) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>   
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No Categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $cats->appends(['search' => request()->get('search'), 'perPage' => request()->get('perPage')])->links('vendor.pagination.bootstrap-4') }}
    </div>
    <script>
        function disableEnter(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                return false;
            }
        }
    </script>
@endsection
