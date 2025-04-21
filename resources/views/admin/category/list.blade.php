@extends('layouts.admin')

@section('content')
    <div class="row">
        <v-col cols="12" md="6">
            <h2 class="text-h6 fw-semibold">Categories</h2>
        </v-col>
        <v-col cols="12" md="6" class="text-end">
            <v-btn color="secondary" small href="{{route('mcat.add')}}" class="text-none">
                Create Category
            </v-btn>
        </v-col>
    </div>
    <admin-mcatlist></admin-mcatlist>
@endsection
