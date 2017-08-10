@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    {{-- for single frame --}}
    <style>
        h2 {
            margin-top: 10px !important;
            margin-bottom: 10px;
        }
    </style>
    {!! $return !!}
@endsection