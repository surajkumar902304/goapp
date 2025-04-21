@extends('layouts.user')

@section('content')
<div class="container mt-2">
    <div class="row text-center mb-3">
        @if ($shopStatus === 0)
            <h2><b>{{ strtoupper($shopName) }}</b> is <span class="text-danger">Inactive</span></h2>
        @else
            <h2><b>{{ strtoupper($shopName) }}</b></h2>
        @endif
    </div>
    <div class="row">
        <!-- Product Types Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Product Types</h5>
                    <p class="card-text">Total Product Type:- <strong>{{ $totalProductTypes }}</strong></p>
                    <a href="{{ route('product_types.index') }}" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
        <!-- Brands Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Brands</h5>
                    <p class="card-text">Total Product Brand:- <strong>{{ $totalBrands }}</strong></p>
                    <a href="{{ route('brands.index') }}" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
        <!-- Tags Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Tags</h5>
                    <p class="card-text">Total Product Tag:- <strong>{{ $totalTags }}</strong></p>
                    <a href="{{ route('tags.index') }}" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
