@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->
<form class="form" method="post">
    {{ csrf_field() }}
    <table class="table table-striped">
    @foreach ($context as $lang_key => $lang_value)
            <tr>
                    <td>
                        {!! $lang_key !!}
                    </td>
                    <td>
                        <input class="form-control" name='{!! $lang_key !!}' value="{{ $lang_value }}">
                    </td>
            </tr>
        @endforeach
        </table>
    <input class="btn btn-primary" type="submit" value="Save">
</form>
@endsection