@extends('crud::layout')
@section('title', 'CRUD - role add')
@section('header', 'CRUD - role add')

@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->
    <div class="col-sm-12">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Name</label>
            <div class="input-group ">
                <input type="text" class="form-control col-sm-4" id="name" placeholder="Name">
                <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat" id="save">Go!</button>
                    </span>
            </div>

        </div>

    </div>
        {{--<h4>Add Role</h4>--}}
    <div class="col-md-12 row">
        @include('crud::roles.permissions',[$roles])
    </div>
@endsection
