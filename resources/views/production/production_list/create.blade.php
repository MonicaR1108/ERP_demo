@extends('layouts.dashboard')

@section('title', 'Create Production List - ERP System')

@section('content')
    @include('production.production_list._form', ['mode' => 'create'])
@endsection

