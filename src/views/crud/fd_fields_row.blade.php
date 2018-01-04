<tr{!! isset($f->exists_in_table) ? '' : ' class="bg-warning"' !!}>
    <td>
        <input type="hidden" class="form-control" name="id[]" value="{{ isset($f->id) ? $f->id : '' }}">
        <input type="text" class="form-control fd_name" name="name[]" value="{{ $f->name }}">
        <input type="hidden" class="form-control fd_sort" name="sort[]" value="{{ $f->sort }}">
    </td>
    <td><!--<input type="text" name="type[field]" value="{{ $f->type }}">-->
        <select class="form-control type" name="type[]" onclick="fd_type_changed(this);">
            @foreach($field_types as $ft_k => $ft)
                <option value="{{ $ft_k }}"{{ $ft_k == $f->type ? ' selected ' : '' }}>{{ $ft }}</option>
            @endforeach
        </select>
        <span class="relation_display" style="display:none;">
            Зв'язок:<br>
            <select class="form-control type" name="relation[]" onclick="fd_relation_changed(this);">
                <option value="" table="">- Виберіть зв'язок -</option>
                @foreach($relations as $rel)
                    <option value="{{ $rel['rel_method_name'] }}"{{ $rel['rel_method_name'] == $f->relation ? ' selected ' : '' }}>
                        {{$rel['rel_method_name']}} ({{ $rel['rel_type'] }}, {{ $rel['rel_left_column'] }}, {{ $rel['rel_right_column'] }})
                    </option>
                @endforeach
            </select>
            Відображати колонку:<br>
            <input class="form-control display_column" name="display_column[]" value="{{ $f->display_column }}" type="text">
            Фільтрувати по: (колонки, через кому)<br>
            <input class="form-control search_columns" name="search_columns[]" value="{{ $f->search_columns }}" type="text">
        </span>
    </td>
    <td><input type="text" class="form-control valid" name="validators[]" id="validator" value="{{ $f->validators }}"></td>
    <td>
        <input type="checkbox" {{--id="grid_show"--}} class="form-control checkbox_autofill" {{ $f->grid_show ? 'checked' : '' }}>
        <input type="hidden" name="grid_show[]">
    </td>
    <td>
        <input type="checkbox" {{--id="grid_filter"--}} class="form-control checkbox_autofill" {{ $f->grid_filter ? 'checked' : '' }}>
        <input type="hidden" name="grid_filter[]">
    </td>
    <td><input type="text" class="form-control" name="grid_custom_display[]" value="{{ $f->grid_custom_display }}"></td>
    {{--<td><input type="text" class="form-control" name="grid_attributes[]" value="{{ $f->grid_attributes }}"></td>--}}
    <td>
        <input type="checkbox" {{--id="form_show"--}} class="form-control checkbox_autofill" {{ $f->form_show ? 'checked' : '' }}>
        <input type="hidden" name="form_show[]">
    </td>
    {{--<td><input type="text" class="form-control" name="form_attributes[]" value="{{ $f->form_attributes }}"></td>--}}
    {{--<td>--}}
        {{--<input type="checkbox" --}}{{--id="show"--}}{{-- class="form-control checkbox_autofill" {{ $f->show ? 'checked' : '' }}>--}}
        {{--<input type="hidden" name="show[]">--}}
    {{--</td>--}}
    <td>
        <nobr>
        <button class="btn btn-success btn-xs btn-move-field" onclick="return fd_field_move_up(this);" title="Up">
            <span class="glyphicon glyphicon-chevron-up"></span>
        </button>
        <button class="btn btn-success btn-xs btn-move-field" onclick="return fd_field_move_down(this);" title="Down">
            <span class="glyphicon glyphicon-chevron-down"></span>
        </button>
        <button class="btn btn-danger btn-xs btn-delete-field" onclick="$(this).parents('tr').remove();return false;" title="Видалити">
            <span class="glyphicon glyphicon-trash"></span>
        </button>
        </nobr>
    </td>
</tr>

<script>
  $('input').on('ifChecked', function(event){
       $(this).attr('checked', 'checked')
   });
  $('input').on('ifUnchecked', function(event){
      $(this).attr('checked', false)
  });
</script>