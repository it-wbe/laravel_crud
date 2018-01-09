@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'User Settings')

@section('content')
    <h3>Content type show on Dashboard</h3>
    <form class="form" method="post">
        {{ csrf_field() }}
        @foreach($types as $type)
            <div class="col-md-4">
                <h4 class="text-center">{{ $type->name }}</h4>
                <div class="col-md-6 ">
                   <div><input type="radio" name="content[types][{!!$type->id!!}]" value="0" @if(!isset($settings[$type->id])) checked @endif ></div>
                    <label for="{!!$type->id!!}">Don't Show</label>
                </div>
                <div class="col-md-6 ">
                    <div><input type="radio" name="content[types][{!!$type->id!!}]" value="1" @if(isset($settings[$type->id])) checked @endif></div>
                    <label for="{!!$type->id!!}">Show</label>
                </div>
            </div>
        @endforeach
        <input type="submit" class="btn btn-primary col-md-12" value="OK">
    </form>

@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('input').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endsection