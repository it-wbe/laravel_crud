<?php

namespace Wbe\Crud\Controllers\Rapyd;

use Wbe\Crud\Models\ModelGenerator;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;

use App\Models\ContentTypes\Markets;
use App\Models\ContentTypes\News;

use App\Models\ContentTypes\Outcome;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;

use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;
use Zofe\Rapyd\Rapyd;


class EditController extends Controller
{
    /**
     * Форма редагування запису поточного типу контенту, його видалення
     * @param Request $r
     * @param $content_type
     * @param int $lang_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function index(Request $r, $content_type, $lang_id = 0)
    {
        if ($r->exists('modify') || $r->exists('insert')) {

            $content = ContentType::find($content_type);
            if (!$content)
                die('content type with id ' . $content_type . ' not found');

            $content_id = $r->exists('modify') ? $r->input('modify') : false;
            $content->rec_id = $content_id;

            if (!$content) abort('403', 'Content type #' . $content_type . ' not found!');

            //$content_model = 'App\Models\\' . $content->model;
            $content_model = $content::getCTModel($content->model);
            //echo $content_model;
            if (!$content_model)
                die('model not found: ' . $content->model);

            if ($r->exists('modify') || $r->exists('content')) {
                if(\Schema::hasColumn($content->table, 'id')) {
                    $new_content_model = $content_model::where('id', $r->input('modify'))->first();
                } else {
                    $new_content_model = $content_model::where(\Schema::getColumnListing($content->table)[0], $r->input('modify'))->first();
                }
                if (!$new_content_model)
                    $new_content_model = new $content_model;
            } else {
                $new_content_model = (new $content_model);
            }


            $desc_table = $content->table . '_description';
            $desc_table_exists = \Schema::hasTable($desc_table);

            $new_content_model::saved(function ($row) use ($content, $content_id, $desc_table, $desc_table_exists) {
                if ($desc_table_exists && isset($_POST[$desc_table])) {
                    $lang_records = \Request::all()[$desc_table];
                    foreach ($lang_records as $post_lang_id => $post_lang) {
                        $lang_records[$post_lang_id]['content_id'] = $row->id;
                        $lang_records[$post_lang_id]['lang_id'] = $post_lang_id;
                    }

                    foreach ($lang_records as $post_lang_id => $post_lang) {
                        \DB::table($desc_table)->updateOrInsert(['content_id' => $content_id, 'lang_id' => $post_lang_id], $post_lang);
                    }
                }
            });
            // var_export($new_content_model);
            // die;
            $edit = DataForm::source($new_content_model);

            $edit->attributes(array("class" => "table table-striped"));

            $edit->label($content->name . ' > ' .  trans('crud::common.content_add'));

            FieldsProcessor::addFields($content, $edit, 'form');

           $tab = FieldsProcessor::$needTab;


            $edit->link(url('admin/crud/grid/' . $content_type . '/'), trans('crud::common.cancel'), "TR");
            $edit->submit('Save', 'TR');

            $edit->saved(function () use ($edit, $content_type, $lang_id) {
                //\Redirect::to(url('admin/'));
                //redirect()->action('Backend\HomeController@index');
                //return redirect('admin/');
                ///die('<meta http-equiv="refresh" content="0; URL=\'' . url('admin/crud/grid/' . $content_type . '/') . '\'" />');

                if (\Request::has('to'))
                    $back_url = \Request::input('to');
                else
                    $back_url = url('admin/crud/grid/' . $content_type . '/');

                header('Location: ' . $back_url);
                die();
                //die('<meta http-equiv="refresh" content="0; URL=\'' . $back_url . '\'" />');
            });

            $edit->build();

            return view('crud::crud.form', compact('edit', 'lang_id','tab'));

        } elseif ($r->exists('delete')) {

            $content = ContentType::find($content_type);
            $content_model = /*'App\Models\\' .*/ $content->model;
            $new_content_model = $content_model::where('id', $r->input('delete'))->first();
                if (!$new_content_model)
                    return 'content type does not exists';

            $new_content_model->delete();

            return redirect(url('admin/crud/grid/' . $content_type . '/'));
        } else abort(404, 'Action not found');
    }
}
