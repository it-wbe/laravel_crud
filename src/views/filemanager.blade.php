@extends('crud::layout')

@section('title', 'File Manager')
@section('header', 'File Manager')

@section('scripts')

    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



    {!! Html::script('packages/barryvdh/elfinder/js/elfinder.min.js') !!}
    {!! Html::style('packages/barryvdh/elfinder/css/elfinder.min.css') !!}
    {!! Html::style('packages/barryvdh/elfinder/css/theme.css') !!}
@endsection

@section('content')

    <script type="text/javascript" charset="utf-8">
        $().ready(function() {
            var elf = $('#elfinder').elfinder({
                // lang: 'ru',             // language (OPTIONAL)
                url: "{!! url('elfinder/connector') !!}",  // connector URL (REQUIRED)
                height: 600
            }).elfinder('instance');
        });
    </script>

    <!-- Element where elFinder will be created (REQUIRED) -->
    <div id="elfinder"></div>

@endsection