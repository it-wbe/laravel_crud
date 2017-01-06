@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    <script>
        $(function () {
            $('table.table tr td:last-child a[title="Delete"]:has(span.glyphicon-trash)').click(function () {
                if (!confirm('Дійсно видалити?'))
                    return false;
            });
        });
    </script>
    {!! $filter !!}
    <br>
    @include('crud::crud.contentinfo')
    {!! $grid !!}
@endsection