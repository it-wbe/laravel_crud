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

    {{-- tabs with languages--}}
    <ul class="nav nav-tabs">
    @foreach($langs as $lang_name=> $lang_code)
            <li @if($loop->index ==0) class="active" @endif><a data-toggle="tab" href="#{{$lang_code}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
<form class="form" method="post">
    {{ csrf_field() }}
    <div class="tab-content">
        @foreach($langs as $lang_name=> $lang_code)
            {{--{!! dd($lang_name) !!}--}}
            <div id="{{$lang_code}}" class="tab-pane fade in @if($loop->index ==0) active @endif">
                @if(isset($context[$lang_code])!=false)
                <table class="table table-striped">
                @foreach ($context[$lang_code] as $lang_key => $lang_value)
                    <tr>
                            <td>
                                {!! $lang_key !!}
                            </td>
                            <td>
                                <input class="form-control" name='{!!$lang_code.$lang_key !!}' value="{{ $lang_value }}">
                            </td>
                    </tr>
                @endforeach
                </table>
                    @else
                       <h2 class="text-center">мовний файл для цієї мови відсутній</h2>
                    @endif
            </div>
        @endforeach
    </div>
    <input class="btn btn-primary" type="submit" value="Save">
</form>
@endsection