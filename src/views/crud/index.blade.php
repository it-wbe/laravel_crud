@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    @if($content_types)

        <style type="text/css">
            .log{
                border: 1px solid black;
                margin: 5px;
            }
        </style>

        {{-- Total--}}
        @foreach($counts as $count)
            <div class="col-md-{!! 12 /config('crud.index_count_total')!!}">
                <div class="panel-title">Total {{$count['name']}}</div>
                <div class="panel-body"><h1>{{$count['count']}}</h1></div>
            </div>

            @if($loop->index == config('crud.index_count_total')-1)
                @break
            @endif
        @endforeach
        {{-- End Total--}}
        {{-- Add content --}}
        <div class="panel col-md-6" style="border:1px solid black;">
            <div class="panel-title">Add Content:</div>
            <div class="panel-body">
                <div class="col-md-8">
                        <form >
                            <select id="add_content_select" class="form-control">
                                @foreach($content_types as $type)
                                    <option value="/admin/crud/edit/{{$type->id}}?insert=1">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </form>
                </div>
                <div class="col-md-2">
                    <button id="add_content_ok" class="btn btn-success btn-lg">OK</button>
                </div>
            </div>
        </div>
        {{--End Add Content--}}

        {{-- Log --}}
        <div class="col-md-6">
            <div class="panel-title">Last Activity:</div>
            <div class="panel-body">
                @foreach($logs as $log)
                <div class="col-md-12 log">
                    <h3>{{$log->user->name}}</h3>
                    <div class="col-md-6">{{$log->action}}</div>
                    <div class="col-md-6">{{$log->content_type->name}}</div>
                    <div class="col-md-12">{{$log->action_date}}</div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- End Log --}}
    @endif
@endsection
@section('scripts')
<script type="text/javascript">
   $("#add_content_ok").on('click',function(){
       console.log($('#add_content_select').find(":selected").val());
       window.location = $('#add_content_select').find(":selected").val();
   });
</script>
@endsection