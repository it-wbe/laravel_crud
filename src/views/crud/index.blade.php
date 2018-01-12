@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')

        <style type="text/css">
            .log{
                border: 1px solid black;
                margin: 5px;
            }
        </style>
        @if($counts)
             {{--Total--}}
            @foreach($counts as $count)
                <div class="col-md-3 col-sm-6 col-xs-6 info-box_dashboard-wrap">
                   <div class="info-box info-box_dashboard">
                       <div class="info-box-icon bg-aqua"><h1>{{$count['count']}}</h1></div>
                       <div class="info-box-content info-box-content_dashbord">Total {{$count['name']}}</div>
                   </div>
                </div>
            @endforeach
             {{--End Total--}}
        @endif
        @if(count($content_types)>0)
            {{-- Add content --}}
        <div class=" col-md-6 col-sm-12 col-xs-12">
           <div class="box box-info">
                <div class="box-header">{!! __('crud::common.add_content') !!}:</div>
                <div class="box-body">
                    <div class="input-group">
                            <form >
                                <select id="add_content_select" class="form-control">
                                    @foreach($content_types as $type)
                                        <option value="/admin/crud/edit/{{$type->id}}?insert=1">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </form>
                            <span class="input-group-btn">
                                <button id="add_content_ok" class="btn btn-primary">OK</button>
                            </span>
                    </div>
                    <!--<div class="col-md-2">
                        <button id="add_content_ok" class="btn btn-primary btn-lg">OK</button>
                    </div>-->
                </div>
            </div>
        </div>
        {{--End Add Content--}}
            @endif
        @if(count($logs)>0)
        {{-- Log --}}
        <div class="col-md-6 col-sm-12 col-xs-12">
           <div class="box box-info">
            <div class="box-header">{!! __('crud::common.last_activity')!!}:</div>
            <div class="box-body">
                @foreach($logs as $log)
                <div class="col-md-12 small-box small-box_dashboard">
                    <div class="inner clearfix inner_flex-wrap">
                      <div class="col-md-5 col-xs-5 nopadding inner_flex-wrap">
                         <div class="icon_dashboard">
                             <i class="fa fa-user"></i>
                         </div>
                          
                          <h3 class="small-box__title">{{$log->user->name}}</h3>
                      </div>
                        <div class="col-md-7 col-xs-7 nopadding">
                            <div class="col-md-6 col-sm-6 col-xs-12">{{$log->action}}</div>
                            <div class="col-md-6 col-sm-6 col-xs-12">{{$log->content_type->name}}</div>
                            <div class="col-md-12 small-box__date">{{$log->action_date}}</div>
                        </div>
                        
                    </div>
                </div>
                @endforeach
            </div>
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