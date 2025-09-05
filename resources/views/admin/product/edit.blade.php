@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2>Edit Product</h2>
  
    <admin-editproduct :mproid="{{$mproductid}}"></admin-editproduct>
</div> 
@endsection