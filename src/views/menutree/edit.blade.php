@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    @if(isset($edit))
        <form method="post" class="form"  >
            {{ csrf_field() }}
            @foreach($edit as $edit_val)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="title">{{$langs[$edit_val['lang_id']]}}</label>
                        <input type="text" class="form-control" name="title[{{$edit_val['lang_id']}}][title]" value="{{$edit_val['title']}}" required id="title">
                        <input type="hidden"  name="title[{{$edit_val['lang_id']}}][lang_id]" value="{{$edit_val['lang_id']}}" required >
                    </div>
                </div>
            @endforeach
            <button type="submit" class="btn btn-primary col-md-4 col-xs-4">Edit Node</button>
            <a href="{{route('Menu Edit')}}" class="btn btn-default col-md-4 col-xs-4 pull-right">Back</a>
        </form>
    @else
        <div class="col-md-6 col-md-offset-3">
           <h1>
            Realy Delete Node ?
           </h1>
        </div>
        <button onclick="location.href='{{route('Menu Edit')}}'" class="btn btn-default col-md-4 col-xs-4">Cancel</button>
        <form method="post" class="form"  >
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary col-md-4 col-xs-4 pull-right">Delete</button>
        </form>

    @endif
@endsection