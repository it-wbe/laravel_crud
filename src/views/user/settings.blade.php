@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'User Settings')

@section('content')
    <h3>Content type show on Dashboard</h3>
    <form class="form" method="post">
        {{ csrf_field() }}
        @foreach($types as $type)
            <div class="col-md-4 col-sm-6 col-xs-6 settings__block">
               <div class="box">
                   <div class="box-header with-border">
                       <h4 class="text-center settings__title">{{ $type->name }}</h4>
                   </div>
                    
                    <div class="box-body">
                       <div class="settings__flex-wrap">
                            <div class="col-md-6 settings__flex-wrap">
                               <div><input type="radio" name="content[types][{!!$type->id!!}]" value="0" @if(!isset($settings[$type->id])) checked @endif ></div>
                                <label for="{!!$type->id!!}">Don't Show</label>
                            </div>
                            <div class="col-md-6 settings__flex-wrap">
                                <div><input type="radio" name="content[types][{!!$type->id!!}]" value="1" @if(isset($settings[$type->id])) checked @endif></div>
                                <label for="{!!$type->id!!}">Show</label>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        @endforeach
        <div class="text-center col-xs-12">
            <input type="submit" class="btn btn-primary settings__btn" value="OK">
        </div>
        
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