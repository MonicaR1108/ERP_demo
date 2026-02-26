@extends('layouts.dashboard')

@section('title', 'View Production List - ERP System')

@section('content')
    @include('production.production_list._form', ['mode' => 'view'])
@endsection

