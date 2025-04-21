@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 class="text-h6 fw-semibold">Master Products</h2>
    </div>
    <div class="col-md-6 text-end">
        <v-btn color="secondary" small href="{{route('adminproduct.addview')}}" class="text-none font-weight-bold">
            Add Product
        </v-btn>
        <v-btn color="secondary" outlined class="text-none font-weight-bold" small>Export</v-btn>
        <v-btn color="secondary" outlined class="text-none font-weight-bold" small>Import</v-btn>
    </div>
    <admin-productslist></admin-productslist>
</div>

@endsection
