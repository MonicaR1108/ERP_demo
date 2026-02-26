@extends('layouts.dashboard')

@section('title', 'Edit Production List - ERP System')

@section('content')
    @include('production.production_list._form', ['mode' => 'edit'])
@endsection

