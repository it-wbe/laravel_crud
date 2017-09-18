<div class="col-md-12 relations" id="{{$id}}" @if(!$display) style="display:none;" @endif>
    <a class="btn btn-danger relation_delete pull-right" id="{{$id}}"><span class="glyphicon glyphicon-minus"></span></a>
    @foreach ($filds as $key_fild => $value)
      @if(!is_array($value))
        <div class="form-group clearfix @if(!empty($value->message)&&$value->label !='id')has-error @endif" id="{{$id.$value->label}}">
          @if($value->label !='id')
              <div class="col-md-2">
                  <label for="relations{{$id.$value->label}}" class="col-sm-2 control-label required">{{$value->label}}</label>
                  <label for="relations{{$id.$value->label}}" id="{{$id.$value->label}}-error" class="error text-danger active"></label>
              </div>
          @endif
            <div class="col-md-10">
                @if($value->type =='image'&&!is_null($value->value))
                    <img src="{{$value->value}}" class="img-thumbnail" alt="" width="300" height="250">
                @endif
                {!! $value->output!!}
                @if(!empty($value->message))
                    <span class="help-block"><span class="glyphicon glyphicon-warning-sign">{!! $value->message !!}</span></span>
                @endif
            </div>
        </div>
        @endif
    @endforeach

{{-- lang header menu --}}
<ul class="nav nav-tabs">
    @foreach ($langs as $lang_key => $lang_value)
       <li @if($loop->first)  class="active" @endif>
         <a data-toggle="tab" href="#{{$lang_key.$id}}">{{ $lang_value }}</a>
       </li>
    @endforeach
</ul>
{{-- lang content --}}
<div class="tab-content">
  @foreach ($langs as $lang_key => $lang_value)
    <div id="{{$lang_key.$id}}" class="tab-pane fade in @if($loop->first)active @endif">
      @foreach ($filds['desc'][$lang_key] as $desc_key => $desc_value)
        <div class="form-group clearfix @if(!empty($desc_value->message))has-error @endif" id="fg_{!!$id.$lang_key.$desc_value->label !!}">
          <label for="{{$id.$lang_key.$desc_value->label }}" class="col-sm-2 control-label required">{{$desc_value->label}}</label>
            <div class="col-sm-10" id="{!!$id.$lang_key.$desc_value->label !!}">
                  <div class="form-group clearfix " id="fg_{!!$id.$lang_key.$desc_value->label !!}">
                      @if($desc_value->type =='image'&&!is_null($desc_value->value))
                          <img src="{{$desc_value->value}}" class="img-thumbnail" alt="" width="300" height="250">
                      @endif
                    {!! $desc_value->output !!}
                      @if(!empty($desc_value->message))
                          <span class="help-block"><span class="glyphicon glyphicon-warning-sign">{!! $desc_value->message !!}</span></span>
                      @endif
                  </div>
            </div>
        </div>
      @endforeach
    </div>
  @endforeach
</div>
</div>
