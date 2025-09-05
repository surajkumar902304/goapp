@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <v-row>
        <v-col cols="12" md="6">
            <div class="text-h6 fw-semibold">Dashboard</div>
        </v-col>
        <v-col cols="12" md="6"></v-col>
    </v-row>

    <admin-dashboard></admin-dashboard>

</div>
@endsection
