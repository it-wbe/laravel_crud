@extends('crud::layout')
@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    {{-- for single frame --}}
    <style>
        h2 {
            margin-top: 10px !important;
            margin-bottom: 10px;
        }
        .tab-content{
            margin-top: 20px;
        }
    </style>
    <div class="rpd_dataform">
        {!! $edit->header!!}
<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
        @foreach($cont_tab as $key =>$value)
            {{--{!! dd($cont_tab)!!}--}}
                {{--навігація для мов--}}
                    <li @if($loop->index==0){{'class=active'}}@endif>
                        <a data-toggle="tab" href="#tab_cont_{{$key}}">
                            @switch($key)
                                @case(0)
                                    Data
                                @break
                                @case(1)
                                    Description
                                @break
                                @case(3)
                                    Meta
                                @break
                                @case(2)
                                    @foreach($edit->fields as $key=> $value)
                                    @if($value->attributes['tab'] ==2)
                                        {!! $value->label !!}
                                    @endif
                                    @endforeach
                                @break
                            @endswitch
                        </a>
                    </li>
        @endforeach
        </ul>
</div>
        <div class="tab-content">
            {{--контент таби для мов--}}
            {{--{!! dd($cont_tab) !!}--}}
            @foreach($cont_tab as $comt_tab_key =>$cont_tab_value)
                <div id="tab_cont_{{$loop->index}}" class="tab-pane fade @if($loop->index==0){{'in active'}}@endif">
                @foreach($edit->fields as $key=> $value)
{{--                    {!! dd($edit->fields )!!}--}}
{{--                    {!! dump($comt_tab_key) !!}--}}
                    @if($value->attributes['tab'] == $comt_tab_key)
                     {{--якщо е таби та поточне поле має бути в табі вивалюємо таби--}}
                        @if(isset($tab)&&$key == reset($tab)[0])
							<div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                             {{--навігація для мов--}}
                            @foreach($tab[0] as $tab_index=>$tab_key)
                                <li @if($tab_index==0){{'class=active'}}@endif><a data-toggle="tab" href="#tab_{{$tab_index}}">{{explode(')',explode('(',$edit->field($tab_key)->label)[1])[0]}}</a></li>
                            @endforeach
                            </ul>
							</div>
                                <div class="tab-content">
                                     {{--контент таби для мов--}}
                                @foreach($tab[0] as $tab_index=>$tab_key)
                                <div id="tab_{{$tab_index}}" class="tab-pane fade @if($tab_index==0){{'in active'}}@endif">
                                @foreach($tab as $index)
                                    {{--{!! dd($tab) !!}--}}
                                        <div class="form-group clearfix{{-- @if($edit->field($tab[$loop->index][$tab_index])->has_error){{'has-error'}}@endif--}}" id="fg_{{$key}}">
                                        <label for="div_content_{{$tab_index}}_{{$loop->index}}" class="col-sm-2 control-label required">{{explode(' ',$edit->field($tab[$loop->index][$tab_index])->label)[0]}}</label>
                                            <div class="col-sm-10" id="div_content_{{$tab_index}}_{{$loop->index}}">
                                                {!! (html_entity_decode($edit->field($tab[$loop->index][$tab_index])->output))  !!}
                                            @if($edit->field($tab[$loop->index][$tab_index])->has_error)
                                                    @foreach($edit->field($tab[$loop->index][$tab_index])->messages as $message)
                                                    <span class="help-block">
                                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                                    {{$message}}
                                                    </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endforeach
                                </div>
                                @break;
                            @else
                                <div class="form-group clearfix @if($value->has_error){{'has-error'}}@endif" id="fg_{{$key}}">
                                    <label for="{{$key}}" class="col-sm-2 control-label required">{{$key}}</label>
                                    <div class="col-sm-10" id="div_{{$key}}">
                                        {!! $value->output!!}
                                        @if($value->has_error)
                                            @foreach($value->messages as $message)
                                                <span class="help-block">
                                        <span class="glyphicon glyphicon-warning-sign"></span>
                                                    {{$message}}
                                        </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
                {!! $edit->footer!!}
    </div>
@endsection