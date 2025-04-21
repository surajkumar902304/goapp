@extends('layouts.user')

@section('content')
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Product List</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('product.add') }}" class="btn btn-outline-danger">Add Product</a>
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
        <form action="{{ route('product.index') }}" method="GET" class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="pe-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Search by product title..." 
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
    <div class="table-responsive flex-grow-1">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Variant</th>
                    <th>Tags</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <img src="{{ config('app.cdn_url').$product->product_image }}" alt="{{ $product->product_title }}" width="50px" height="50px">
                        </td>
                        <td>
                            <div class="mb-2">{{ $product->product_title }}</div>
                            <div>
                                @if($product->variants)
                                    @php
                                        $optionIds = $product->variants->option_ids ? json_decode($product->variants->option_ids, true) : [];
                                        $options = $product->variants->options ? json_decode($product->variants->options, true) : [];
                                    @endphp
                            
                                    @if(is_array($optionIds) && is_array($options) && count($optionIds) > 0)
                                        @foreach($optionIds as $id)
                                            @php
                                                $optionName = \App\Models\Option_name::find($id);
                                            @endphp
                                            {{ $optionName->option_name ?? 'Unknown Option' }}: {{ $options[$id] ?? 'N/A' }}<br>
                                        @endforeach
                                    @else
                                        <p>No options select</p>
                                    @endif
                                @else
                                    <p>No variants available.</p>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>SKU: {{ optional($product->variants)->sku ?? 'N/A' }}</div>
                            <div>Quantity: {{ optional($product->variants)->qty ?? 'N/A' }}</div>
                            <div>Price: {{ optional($product->variants)->price ?? 'N/A' }}</div>
                            <div>Product Type: {{ optional($product->variants)->product_type_name ?? 'N/A' }}</div>
                            <div>Brand: {{ optional($product->variants)->brand_name ?? 'N/A' }}</div>
                            <div>Slug: {{ $product->product_slug ?? 'N/A' }}</div>
                        </td>
                        <td>
                            @foreach ($product->atags as $tag)
                               <span class="badge bg-success">{{$tag->tag_name}}</span> 
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('product.edit', $product->product_id) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No Products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $products->appends(['search' => request()->get('search'), 'perPage' => request()->get('perPage')])->links('vendor.pagination.bootstrap-4') }}
        </div>
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