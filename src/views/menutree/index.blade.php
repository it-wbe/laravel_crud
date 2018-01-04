@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
<style>
     .datatree-item{
        padding-bottom: 5px;
         padding-top: 5px;
    }
    .datatree-item > button{
        margin: 6px 0px;
    }
</style>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <h4>Add Node</h4>
    <div class="container-fluid" style="border-bottom: 1px solid grey; margin-bottom: 30px; padding-bottom: 10px;">
    <form method="post" action="{!! route('menu.addCustomNode') !!}" class="form"  >
        {{ csrf_field() }}
        @foreach($langs as $lang_id=>$lang_name)
            <div class="col-md-3">
                <div class="form-group">
                    <label for="title">{{$lang_name}}</label>
                    <input type="text" class="form-control" name="title[{{$lang_id}}][title]" required id="title">
                    <input type="hidden"  name="title[{{$lang_id}}][lang_id]" value="{!! $lang_id!!}" required >
                </div>
            </div>
        @endforeach
        <div class="col-md-6 ">
            <label for="icon">Icon</label>
            <input type="text" name="icon" class="form-control">
        </div>
        <div class="col-md-6" style="margin-bottom: 10px;">
            <label for="type">Type</label>
            <select name="type" class="form-control">
                <option value="1">Пункт Menu</option>
                <option value="12">Label</option>
            </select>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary col-md-4 col-xs-4">Add Node</button>
        </div>
    </form>
<div class="col-md-6">
    <a class="btn btn-danger  pull-right col-md-4 col-xs-4" href="{!! route('menu.generate') !!}">Regenerate Menu</a>
</div>
    </div>
<div class="container-fluid" >
    {!! $tree !!}
</div>
@endsection