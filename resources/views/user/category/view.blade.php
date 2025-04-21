@extends('layouts.user')

@section('title', 'View Category')

@section('content')
    <div class="container mt-5">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2>Products</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('category.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <img src="{{ config('app.cdn_url').$product->product_image }}" alt="{{ $product->product_title }}" width="50px" height="50px">
                        </td>
                        <td>{{ $product->product_title }}</td>
                        <td>{{ $product->variants->price }}</td>  
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No products found.</td>
                    </tr>
                @endforelse
            </tbody>               
        </table>
    </div>
@endsection
