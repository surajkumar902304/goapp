@extends('layouts.admin')

@section('content')
<div class="container-fluid">  
    <admin-editmsubcat :msubcatid="{{$msubcatid}}"></admin-editmsubcat>
</div> 
@endsection