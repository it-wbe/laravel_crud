@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')
@section('show_sidebar', false)

@section('scripts')
    <ul class="validator_arr" style="display:none; height: 100px; overflow: auto;
  overscroll-behavior: contain;">
       <li class="active" tabindex="0">required</li>
       <li tabindex="0">accepted</li>
        <li tabindex="0">active_url</li>
        <li tabindex="0">after</li>
        <li tabindex="0">after_or_equal</li>
        <li tabindex="0">alpha</li>
        <li tabindex="0">alpha_dash</li>
        <li tabindex="0">alpha_numeric</li>
        <li tabindex="0">array</li>
        <li tabindex="0">before</li>
        <li tabindex="0">before_or_equal</li>
        <li tabindex="0">between</li>
        <li tabindex="0">boolean</li>
        <li tabindex="0">confirmed</li>
        <li tabindex="0">date</li>
        <li tabindex="0">date_equals</li>
        <li tabindex="0">date_format</li>
        <li tabindex="0">different</li>
        <li tabindex="0">digits</li>
        <li tabindex="0">digits_between</li>
        <li tabindex="0">dimensions</li>
        <li tabindex="0">distinct</li>
        <li tabindex="0">e-mail</li>
        <li tabindex="0">exists</li>
        <li tabindex="0">file</li>
        <li tabindex="0">filled</li>
        <li tabindex="0">image</li>
        <li tabindex="0">in</li>
        <li tabindex="0">in_array</li>
        <li tabindex="0">integer</li>
        <li tabindex="0">ip_address</li>
        <li tabindex="0">json</li>
        <li tabindex="0">max</li>
        <li tabindex="0">mime_types</li>
        <li tabindex="0">min</li>
        <li tabindex="0">nullable</li>
        <li tabindex="0">not_in</li>
        <li tabindex="0">numeric</li>
        <li tabindex="0">present</li>
        <li tabindex="0">regular_expression</li>
        <li tabindex="0">required</li>
        <li tabindex="0">required_if</li>
        <li tabindex="0">required_unless</li>
        <li tabindex="0">required_with</li>
        <li tabindex="0">required_with_all</li>
        <li tabindex="0">required_without</li>
        <li tabindex="0">required_without_all</li>
        <li tabindex="0">same</li>
        <li tabindex="0">size</li>
        <li tabindex="0">string</li>
        <li tabindex="0">timezone</li>
        <li tabindex="0">unique</li>
        <li tabindex="0">url</li>
    </ul>
<script>
        var curent_val_input = null;
        var activeButton = 0;
        $(".valid").on('click',function(){
            var nod = $('.validator_arr').clone(true);
            activeButton = $(nod[0]).children('.active').index();
            $(nod).appendTo($(this).parent());
            $(nod).css('display','block');
            curent_val_input = this;
            $($(this).parent()).on('mouseleave',function(){
                var node =$(this).find('.validator_arr');
                curent_val_input = null;
                node.remove();
            });
        });

        $(".validator_arr li").on('mouseenter',function(){
            $(this).addClass('active');
        });
        $(".validator_arr li").on('mouseleave',function(){
            $(this).removeClass('active');
        });
        $(".validator_arr li").on('click',function(){
            PutSelected(this);
//            console.log(curent_val_input);
//            $(this).innerText
        });
        function PutSelected(selected) {

            if(curent_val_input !=null && curent_val_input.value.lenght !=0){
                if(curent_val_input.value[--curent_val_input.value.lenght] == "|"){
                    /// поставить значение
                    curent_val_input.value+= selected.innerHTML;
                }else{
                    /// поставить | потом значение
                    curent_val_input.value+= "|"+selected.innerHTML;
                }
                console.log(curent_val_input.value[--curent_val_input.value.lenght]);
            }
        }
        function SetFocusTop(div){
            $(div).animate({
                    scrollTop: $(div).prev().position().top
                }, 10);
        //$(div).focus();
        }

        function SetFocusDown(div){
            $(div).animate({
                    scrollTop: $(div).prev().position().bottom
                }, 10);
        //$(div).focus();
        }
        $(function () {
            $(".valid").each(function (index) {
                $(this).keydown(function (e) {
                    ///// up
                    if (e.which == 38) {
                        //debugger;
                        if (activeButton > 0) {
                            console.log('up');
                            var all = $($($(this).parent().children()[1]).children()).length;
                            var classThis = $($($(this).parent().children()[1]).children());
                            $(classThis[activeButton]).removeClass('active');
                            activeButton--;
                            $(classThis[activeButton]).addClass('active');
                            SetFocusDown(classThis[activeButton]);
                            classThis[activeButton].scrollIntoView(false);
                        }
                    }
                    /////enter
                    else if (e.which == 13) {
                        e.preventDefault();
                     var selected = $($(this).parent().children()[1]).children().find(".active");
                        PutSelected(selected);
                     ///////down
                    } else if (e.which == 40) {
                        var all = $($($(this).parent().children()[1]).children()).length;

                        console.log('down');
                        //debugger;
                        if ($($($(this).parent().children()[1]).children()).length > 0 && activeButton < all - 1) {
                            var classThis = $($($(this).parent().children()[1]).children());
                            $(classThis[activeButton]).removeClass('active');
                            activeButton++;
                            SetFocusTop(classThis[activeButton]);
                            $(classThis[activeButton]).addClass('active');
                            classThis[activeButton].scrollIntoView(false);
                        }
                    }
                });

            });
        });
</script>
@endsection

@section('content')
<style>
    .validator_arr{
        z-index: 999999;
        display: inline-block;
        position: absolute;
        background-color: whitesmoke;
        list-style-type: none;
        padding: 5px;
        width: 180px;
        text-align: center;
    }
    .validator_arr>.active{
        color: #1c2d3f;
        background-color: lightslategrey;
    }
</style>



    <style>
        .table-fields input {
            border: 1px solid #ddd;
        }
        .table-fields .form-control {
            padding: 6px 7px !important;
        }

        .table-rel tr td:last-child {
            width:1%;
            white-space:nowrap;
        }

        #btn-clear-fields, #btn-generate-fields {
            margin-left: 15px;
        }

        td [name='form_sort[]'] {
            width: 35px;
        }

        .validators_list{
            display:none;

        }

        /*.table-fields input:focus {
            width:600px;
        }*/
    </style>
    <script>
        function str_before(haystack, needle) {
            return haystack.substr(0, haystack.indexOf(needle));
        }

        function rel_content_type_changed(elem) {
            var tr = $(elem).parents('tr');
            var right_table = tr.find('select[name="rel_right_content_type[]"] option:selected').attr('table');

            tr.attr('content_type', $(elem).val());

            //$(elem).find('option[value=' + $(elem).val() + ']').attr('table') + '.id'
            rel_relation_changed(tr.find('select.rel_type'));

            tr.find('input.rel_table_to').val(
                    $('input#content_table').val() +
                    '_to_' +
                    right_table
            );

            tr.find('input.rel_method_name').val(right_table);
        }

        function rel_relation_changed(elem) {
            var tr = $(elem).parents('tr');
            var right_table = tr.find('select[name="rel_right_content_type[]"] option:selected').attr('table');

            $(elem).parent().find('span.rel_table_to').toggle($(elem).val() == 'belongsToMany');
            if ($(elem).val() == 'belongsToMany') {
                tr.find('.rel_left_column').val($('input#content_table').val());
                tr.find('.rel_right_column').val(right_table);
            } else {
                tr.find('.rel_left_column').val('id');
                tr.find('.rel_right_column').val('id');
            }
        }

        function fd_field_move_up(elem) {
            var elem_tr = $(elem).parents('tr');
            if (!elem_tr) return false;
            var prev_tr = elem_tr.prev('tr');
            if (!prev_tr) return false;
            prev_tr.insertAfter(elem_tr);
            return false;
        }
        function fd_field_move_down(elem) {
            var elem_tr = $(elem).parents('tr');
            if (!elem_tr) return false;
            var next_tr = elem_tr.next('tr');
            if (!next_tr) return false;
            next_tr.insertBefore(elem_tr);
            return false;
        }
        function fd_type_changed(elem) {
            //$(elem).parent().find('span.relation_display').toggle($(elem).val() == 'select');
            $(elem).parent().find('span.relation_display').toggle(
                    $(elem).val() == 'multiselect' ||
                    $(elem).val() == 'select' ||
                    $(elem).val() == 'tags' ||
                    $(elem).val() == 'Wbe\\Crud\\Models\\Rapyd\\Fields\\Img'
            );

            $('table.table-rel input.rel_method_name[original_value="' +''+ '"]'); //
        }
        function fd_relation_changed(elem) {
            var rel = $('table.table-rel input.rel_method_name[original_value="' + $(elem).val() + '"]');
            var display_input = $(elem).parents('tr').find('input.display_column');
            if (!display_input.val())
                display_input.val(rel.parents('tr').find('input.rel_left_column').attr('original_value'));
        }

        function rel_left_column_change(elem) {
            $(elem).parent().find('input.rel_left_column').val($(elem).val());
        }

        /*function add_to_remove_queue(elem) {
            $('form.rel_form').append($("<input>", {
                    type: 'hidden',
                    name: 'hidden',
                    value: $(elem).parents('tr').find('select[name=rel_right_content_type]').val()
                }
            ));
            $(elem).parents('tr').remove();
            return false;
        }*/

        $(function () {
            $('#btn-add-field').click(function () {
                //$('table.table-fields tbody').append($('.table-hover tbody tr:eq(0)').clone());

                $('table.table-fields tbody').append(($('.table-field-newitem tbody tr:eq(0)').clone()));

                /*var new_item = $('.table-field-newitem tbody tr:eq(0)').clone();
                new_item.html((new_item.html).replace('name="default_name[', 'name="default_name['));
                $('table.table-field tbody').append(new_item);*/
                return false;
            });
            $('#button-add-rel').click(function () {
                $('table.table-rel tbody').append($('.table-rel-newitem tbody tr:eq(0)').clone());
                return false;
            });
            $('form.fd_form').submit(function() {
                $('form.fd_form .checkbox_autofill').each(function() {
//                    alert($(this).is(":checked"));
//                    $(this).next('input[name="' + $(this).attr('id') + '[]"]').val($(this).is(":checked") ? 'on' : 'off');
                    $(this).parent('div').next().val($(this).is(":checked") ? 'on' : 'off');
                });

                var i = 0;
                $('form.fd_form input.fd_sort').each(function(){
                    $(this).val(i);
                    i++;
                });
            });

            $('#btn-clear-fields').click(function () {
                if (!confirm('Дійсно очистити?')) return 0;
                $('table.table-fields .btn-delete-field').click();
                $('#btn-save-fields').click();
                return 0;
            });

            $('select.type').each(function () {
                //fd_type_changed(this);
                $(this).trigger("click");
            });



            // www.jqueryscript.net/other/jQuery-Drag-drop-Sorting-Plugin-For-Bootstrap-html5sortable.html
            //$('table.table-fields tbody').sortable({});
        });
    </script>

    @include('crud::common.messages')

    @include('crud::crud.contentinfo')
    <br><br>

    <input type="hidden" id="content_table" value="{{ $content->table }}">



    <div class="pull-right">
        {{--<a href="{!! url('admin/fields_descriptor/content/' . $content->id) !!}" class="btn btn-default">Редагувати поля</a>--}}
        <a href="{!! url('admin/crud/edit/1?modify=' . $content->id . '&to=' . urlencode(url()->full())) !!}" class="btn btn-default">
            <span class="glyphicon glyphicon-edit"></span>
            {{ trans('crud::common.content_type') }}
        </a>
        <a href="{!! url('admin/crud/grid/' . $content->id) !!}" class="btn btn-default">
            <span class="glyphicon glyphicon-edit"></span>
            {{ trans('crud::common.content_data') }}
        </a>
        <a href="{!! url('admin/crud/edit/1?insert=1') !!}" class="btn btn-default" title="{{ trans('crud::common.content_add') }}">
            <span class="glyphicon glyphicon-plus"></span>
        </a>
    </div>

    <ul class="nav nav-tabs">
        <li{!! (!\Request::has('active_tab') || (\Request::input('active_tab') == 'fields')) ? ' class="active"' : '' !!}><a data-toggle="tab" href="#fields">Поля</a></li>
        <li{!! (\Request::input('active_tab') == 'relations') ? ' class="active"' : '' !!}><a data-toggle="tab" href="#relations">Зв'язки</a></li>
        <!--<li><a data-toggle="tab" href="#menu3">Menu 3</a></li>-->
    </ul>

    <div class="tab-content">
        <div id="fields" class="tab-pane fade in{!! (!\Request::has('active_tab') || (\Request::input('active_tab') == 'fields')) ? ' active' : '' !!}">

            <form method="POST" action="" class="fd_form">
                {{ csrf_field() }}
                <input type="hidden" name="active_tab" value="fields">
                <table class="table table-bordered table-hover table-fields">
                    <thead>
                    <tr>
                        <th>name</th>
                        <th>type</th>
                        <th>validators</th>
                        <th>grid show</th>
                        <th>grid filter</th>
                        <th>grid custom display</th>
                        {{--<th>grid attributes</th>--}}
                        <th>form show</th>
                        {{--<th>form attributes</th>--}}
                        {{--<th>show</th>--}}
                        <th>Дії</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fields as $k => $f)
                        @include('crud::crud.fd_fields_row', [
                            'k' => $k,
                            'f' => $f,
                        ])
                    @endforeach
                    </tbody>
                </table>

                <button type="submit" id="btn-save-fields" name="btn-save-fields" class="btn btn-primary">Зберегти схему</button>
                <button id="btn-clear-fields" name="btn-clear-fields" class="btn btn-danger">Очистити поля</button>
                <button type="submit" id="btn-generate-fields" name="btn-generate-fields" class="btn btn-info">Згенерувати опис</button>
                <button id="btn-add-field" name="btn-add-field" class="btn btn-default pull-right">+ Додати поле</button>
            </form>

            <table class="table-field-newitem hidden">
                <tbody>
                @include('crud::crud.fd_fields_row', [
                    'k' => 'default_name',
                    'f' => $default_field,
                ])
                </tbody>
            </table>

        </div>
        <div id="relations" class="tab-pane fade in{!! (\Request::input('active_tab') == 'relations') ? ' active' : '' !!}">



            <form method="POST" action="" class="rel_form">
                {{ csrf_field() }}
                <input type="hidden" name="active_tab" value="relations">
                <input type="hidden" name="existing_relations" value="{{ $existing_relations }}">

                <table class="table table-bordered table-hover table-rel">
                    <thead>
                    <tr>
                        <th>Тип контенту</th>
                        <th>Назва відношення</th>
                        <th>Відношення</th>
                        <th>Колонка зліва</th>
                        <th>Колонка справа</th>
                        <th>Дії</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($relations as $content_id => $rel)
                        @include('crud::crud.fd_relation_row', $rel)
                    @endforeach
                    </tbody>
                </table>
                <button type="submit" id="btn-save-rel" name="btn-save-rel" class="btn btn-primary">Зберегти зв'язки</button>
                <button id="button-add-rel" name="button-add" class="btn btn-default pull-right">+ Додати зв'язок</button>
            </form>



            <table class="table-rel-newitem hidden">
                <tbody>
                    @include('crud::crud.fd_relation_row', [
                        'rel_right_content_type' => '',
                        'rel_method_name' => '',
                        'rel_type' => '',
                        'rel_table_to' => '',
                        'rel_table_to_exists' => '',
                        'left_columns' => $left_columns,
                        'rel_left_column' => 'id',
                        'rel_right_column' => 'id',
                    ])
                </tbody>
            </table>



            @if ($unknown_methods)
                <br>
                <h4>Не розпізнано</h4>
                <code>
                @foreach ($unknown_methods as $unkn_method)
                    {!! print_r($unkn_method) !!}<br>
                @endforeach
                </code>
            @endif




        </div>
        <!--<div id="menu3" class="tab-pane fade">
            <h3>Menu 3</h3>
            <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
        </div>-->
    </div>
@endsection