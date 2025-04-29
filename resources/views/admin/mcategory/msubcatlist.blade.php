@extends('layouts.admin')

@section('content')
    <div class="row">
        <v-col cols="12" md="6">
            <h2 class="text-h6 fw-semibold">Sub-Category</h2>
        </v-col>
        <v-col cols="12" md="6" class="text-end">
            <v-btn color="secondary" small href="{{route('mcoll.add')}}" class="text-none">
                Add Sub-Category
            </v-btn>
        </v-col>
    </div>
    <admin-msubcatlist></admin-msubcatlist>
@endsection