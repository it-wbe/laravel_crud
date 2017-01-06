@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')

@section('scripts')
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



    {!! Html::script('public/packages/barryvdh/elfinder/js/elfinder.min.js') !!}
    {!! Html::style('public/packages/barryvdh/elfinder/css/elfinder.min.css') !!}
    {!! Html::style('public/packages/barryvdh/elfinder/css/theme.css') !!}
@endsection

@section('content')

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Home</a></li>
    <li><a data-toggle="tab" href="#menu1">Menu 1</a></li>
    <li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
    <li><a data-toggle="tab" href="#menu3">Menu 3</a></li>
    <li><a target="_blank" href="{{ url('admin/settings/generate') }}">Generate</a></li>
    <li><a target="_blank" href="{{ url('admin/adminer') }}">Adminer</a></li>
</ul>

<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
            <form class="form-horizontal">
                <fieldset>


                    <!-- Form Name -->
                    <h3>Test Configuration</h3>

                    <!-- Multiple Checkboxes -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="checkboxes">Multiple Checkboxes</label>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label for="checkboxes-0">
                                    <input type="checkbox" name="checkboxes" id="checkboxes-0" value="1">
                                    User has visited the site before
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="checkboxes-1">
                                    <input type="checkbox" name="checkboxes" id="checkboxes-1" value="2">
                                    If User is in a Cart Audience file, exclude from Browse
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Appended Input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="session-length">Session Length</label>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input id="session-length" name="session-length" class="form-control" placeholder="60" type="text" required="">
                                <span class="input-group-addon">min</span>
                            </div>
        <p class="help-block">Defaults to 1 hour, can range from 15 min to 3 hr (15 min increments)</p>
    </div>
</div>
<!-- Appended Input-->
<div class="form-group">
    <label class="col-md-4 control-label" for="delay-event">Delay after Event</label>
    <div class="col-md-2">
        <div class="input-group">
            <input id="delay-event" name="delay-event" class="form-control" placeholder="30" type="text">
            <span class="input-group-addon">min</span>
        </div>
        <p class="help-block">How long after the Session End so you want to add someone to the audience file</p>
    </div>
</div>
<!-- Appended Input-->
<div class="form-group">
    <label class="col-md-4 control-label" for="send-frequency">Sending Frequency</label>
    <div class="col-md-2">
        <div class="input-group">
            <input id="send-frequency" name="send-frequency" class="form-control" placeholder="60" type="text">
            <span class="input-group-addon">min</span>
        </div>
        <p class="help-block">How often do you want to generate the batch audience?</p>
    </div>
</div>
<!-- Button -->
<div class="form-group">
    <label class="col-md-4 control-label" for="button-calculate"></label>
    <div class="col-md-4">
        <button id="button-calculate" name="button-calculate" class="btn btn-primary">Calculate</button>
    </div>
</div>

</fieldset>
</form>


    </div>
    <div id="menu1" class="tab-pane fade">


        <form class="form-horizontal">
            <fieldset>


                <!-- Form Name -->
                <h3>Test Configuration</h3>

                <!-- Multiple Checkboxes -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="checkboxes">Multiple Checkboxes</label>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="checkboxes-0">
                                <input type="checkbox" name="checkboxes" id="checkboxes-0" value="1">
                                User has visited the site before
                            </label>
                        </div>
                        <div class="checkbox">
                            <label for="checkboxes-1">
                                <input type="checkbox" name="checkboxes" id="checkboxes-1" value="2">
                                If User is in a Cart Audience file, exclude from Browse
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Appended Input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="session-length">Session Length</label>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="session-length" name="session-length" class="form-control" placeholder="60" type="text" required="">
                            <span class="input-group-addon">min</span>
                        </div>
                        <p class="help-block">Defaults to 1 hour, can range from 15 min to 3 hr (15 min increments)</p>
                    </div>
                </div>
                <!-- Appended Input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="delay-event">Delay after Event</label>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="delay-event" name="delay-event" class="form-control" placeholder="30" type="text">
                            <span class="input-group-addon">min</span>
                        </div>
                        <p class="help-block">How long after the Session End so you want to add someone to the audience file</p>
                    </div>
                </div>
                <!-- Appended Input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="send-frequency">Sending Frequency</label>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input id="send-frequency" name="send-frequency" class="form-control" placeholder="60" type="text">
                            <span class="input-group-addon">min</span>
                        </div>
                        <p class="help-block">How often do you want to generate the batch audience?</p>
                    </div>
                </div>
                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="button-calculate"></label>
                    <div class="col-md-4">
                        <button id="button-calculate" name="button-calculate" class="btn btn-primary">Calculate</button>
                    </div>
                </div>

            </fieldset>
        </form>


    </div>
    <div id="menu2" class="tab-pane fade">
        <h3>Menu 2</h3>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
    </div>
    <div id="menu3" class="tab-pane fade">

        menu3<br>


        <script type="text/javascript" charset="utf-8">
            $().ready(function() {
                var elf = $('#elfinder').elfinder({
                    // lang: 'ru',             // language (OPTIONAL)
                    url : "{!! url('elfinder/connector') !!}"  // connector URL (REQUIRED)
                }).elfinder('instance');
            });
        </script>

        <!-- Element where elFinder will be created (REQUIRED) -->
        <div id="elfinder"></div>

    </div>
</div>

@endsection