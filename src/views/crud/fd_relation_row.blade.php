<tr{!! !$rel_right_content_type ? ' class="bg-warning"' : '' !!}>
    <td>
        <select class="form-control rel_right_content_type" name="rel_right_content_type[]" onchange="rel_content_type_changed(this)">
            <option value="" table="">- Виберіть тип контенту -</option>
            @foreach($content_types as $ct)
                <option value="{{ $ct->id }}" {{ $rel_right_content_type == $ct->id ? 'selected ' : '' }}table="{{ $ct->table }}">{{ $ct->name }}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" class="form-control rel_method_name" name="rel_method_name[]" original_value="{{ $rel_method_name }}" value="{{ $rel_method_name }}"></td>
    <td>
        <select class="form-control rel_type" name="rel_type[]" onchange="rel_relation_changed(this);">
            <option value="hasOne" {{ $rel_type == 'hasOne' ? 'selected ' : '' }}>hasOne</option>
            <option value="hasMany" {{ $rel_type == 'hasMany' ? 'selected ' : '' }}>hasMany</option>
            <option value="belongsToMany" {{ $rel_type == 'belongsToMany' ? 'selected ' : '' }}>belongsToMany</option>
            <option value="belongsTo" {{ $rel_type == 'belongsTo' ? 'selected ' : '' }}>belongsTo (інверсія hasMany)</option>
        </select>
        <span class="rel_table_to" {!! $rel_type != 'belongsToMany' ? ' style="display:none;"' : '' !!} >
            Таблиця: @if ($rel_table_to_exists)
                <span style="color: green">(існує)</span>
            @else
                <span style="color: red">(буде створено)</span>
            @endif
            <br>
            <input type="text" class="form-control rel_table_to" name="rel_table_to[]" value="{{ $rel_table_to }}">
        </span>
    </td>
    <? /*<td><input type="text" class="form-control rel_left_column" name="rel_left_column[]" original_value="{{ $rel_left_column }}" value="{{ $rel_left_column }}"></td>*/ ?>
    <td>
        Вибрано:<br>
        <input type="text" class="form-control rel_left_column" name="rel_left_column[]" value="{{ $rel_left_column }}">
        Колонки таблиці:<br>
        <select class="form-control rel_left_column" original_value="{{ $rel_left_column }}" onchange="rel_left_column_change(this);">
            <option value="" table="">- Виберіть колонку -</option>
            @foreach($left_columns as $c)
                <option value="{{ $c }}" {{ $rel_left_column == $c ? 'selected ' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" class="form-control rel_right_column" name="rel_right_column[]" original_value="{{ $rel_right_column }}" value="{{ $rel_right_column }}"></td>
    <td>
        <button class="btn btn-danger btn-xs" onclick="$(this).parents('tr').remove();return false;" title="Видалити">
            <span class="glyphicon glyphicon-trash"></span>
        </button>
    </td>
</tr>