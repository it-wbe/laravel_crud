<?php

namespace Wbe\Crud\Controllers\Rapyd;

use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ModelGenerator;

use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;


class GridController extends Controller
{
    /**
     * Таблиця із записами для поточного типу контенту та фільтрами по них
     * @param $content_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($content_type)
    {
        $content = ContentType::find($content_type);

        if (!$content) abort('500', 'Content type #' . $content_type . ' not found!');

        // from content_type_fields
        $ct_fields = ContentTypeFields::getFieldsFromDB($content_type, [['grid_show', '=', \DB::raw(1)]]);

        $fields_schema = \Schema::getColumnListing($content->table);
        $fields_desc_schema = \Schema::getColumnListing($content->table . '_description');

        $classname = $content::getCTModel($content->model);
        $model_filename = $content->getClassFilename($classname);
        $relation_methods = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename));

//        print_r($relation_methods);

        $unsorted_fields = [];

        foreach ($fields_schema as $field) {
            if (isset($ct_fields[$field])) {
                $unsorted_fields[$field] = $ct_fields[$field];
            }
        }

        foreach ($fields_desc_schema as $field) {
            if (isset($ct_fields[$field])) {
                $unsorted_fields[$field] = $ct_fields[$field];
            }
        }

        $fields = [];
        foreach ($ct_fields as $name => $ct_field) {
            if (isset($unsorted_fields[$name])) {
                $fields[$name] = $unsorted_fields[$name];
            }
        }

        $content_type_model = $content::getCTModel($content->model);

        if (!$content_type_model)
            die('model not found: ' . $content->model);

        $new_content_type_model = new $content_type_model;

        //add relations to content model
        $relations = [];
        foreach ($relation_methods as $relation_name => $relation) {
            //if relation type hasOne an relation not lang description
            if(isset($relation[1]) && $relation[1] == 'hasOne' && $relation_name != 'description')$relations[] = $relation_name;
        }

        $filter = DataFilter::source($new_content_type_model::with($relations));

        FieldsProcessor::addFields($content, $filter, 'filter');
        $filter->add('show_rows_crud','show_rows_crud','select')
            ->options([10=>10,20=>20,50=>50]);
        $filter->submit('Знайти');
        $filter->reset('Очистити');
        $filter->build();

        $grid = DataGrid::source($filter);
        $grid->attributes(array("class" => "table table-striped"));

        foreach ($fields as $field) {
            if ($field->grid_show && ($field->name != 'lang_id') && ($field->name != 'content_id')) {
                $display = $field->grid_custom_display ? $field->grid_custom_display : $field->name;
                // $f = $grid->add($display, $field->caption ? $field->caption : $field->name, $field->name);
                $f = $grid->add($display, $field->title != "not set" ? $field->title : $field->name, $field->name);

//                if ($field->grid_attributes)
//                    eval($field->grid_attributes);
            }
        }

        $grid->link(url('admin/crud/edit/1?modify=' . $content_type . '&to=' . urlencode(url()->full())), trans('crud::common.content_type'), "TR");
        $grid->link(url('admin/fields_descriptor/content/' . $content_type), trans('crud::common.content_fields'), "TR");
        $grid->link(url('/admin/crud/edit/' . $content_type . '?insert=1'), trans('crud::common.content_add'), "TR");

        $grid->edit(url('/admin/crud/edit/' . $content_type . '/'), trans('crud::common.grid_actions'), 'modify|delete');

        /*$grid->add('mybutton','mybutton')->cell( function ($value, $row) {

            //$my_custom_condition = $row->something == ....
            //$my_custom_link = route('my.route',['id'=>$row->ID])
            if ($my_custom_condition)
            {
                return $my_custom_link;
            }

        });*/
        $show_rows_crud =  \Request::input('show_rows_crud');
        $grid->paginate($show_rows_crud );
        return view('crud::crud.grid', compact('content', 'filter', 'grid'));
    }
}
