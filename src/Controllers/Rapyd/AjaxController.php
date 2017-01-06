<?php

namespace Wbe\Crud\Controllers\Rapyd;

use Wbe\Crud\Models\Translatable;
use App\Models\ContentTypes\Outcome;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AjaxController extends Controller
{
    public function getAutocomplete(Request $r, $model, $fields, $limit)
    {
        if (!$r->exists("q"))
            return '';

        //'App\Models\\' .
        $model = str_replace('_', '\\', $model);

        //$r->has('fields')
        $fields = $fields ? array_map('trim', explode(',', $fields)) : '';
        /*$model = $r->has('model') ? 'App\Models\\' . $r->input('model') : '';
        $limit = $r->has('limit') ? $r->input('limit') : 10;*/

        /*if (in_array('App\Models\Crud\Translatable', class_uses($model))) {
            $model_builder = $model::translate(session('admin_lang_id'));
        } else {
            $model_builder = new $model;
        }*/
        $model_builder = new $model;

        /*$nn = 'App\Models\ContentTypes\Outcome';
        //$q = \DB::table('outcomes_description')->whereRaw('name LIKE "Over ru%"')->get();
        $q = $nn::translate(session('admin_lang_id'));
        $q = $q->whereRaw('outcomes_description.name LIKE "Over en%" AND lang_id = ' . (int)session('admin_lang_id') . '');
        $q = $q->get()->toArray();*/
        //$q = $nn::translate(session('admin_lang_id'))->whereRaw('outcomes_description.name LIKE "Over en%" AND lang_id = ' . (int)session('admin_lang_id') . '')->get()->toArray();
        //$q->
        //print_r($q); die();


        //die($r->input("q"));
        //print_r($fields);


        if ($fields) {
            if (!is_array($fields))
                $fields = [$fields];

            //$first = 1;

            //echo Input::get('q');

            if (Input::get('q'))
            foreach ($fields as $field) {
                //echo $field;
                //{($first ? 'orW' : 'w') . 'here'}
                $model_builder = $model_builder->orWhereRaw(
                    '' . $field . ' LIKE "' . Input::get('q') . '%"'
                    //'' . (new $model)->getTable() . '_description.' . $field . ' LIKE "Over %"'
                    //'' . (new $model)->getTable() . '.' . $field . ' LIKE "Over %"'
                    //AND lang_id = ' . (int)session('admin_lang_id') . ')' //Input::get('q') $r->input("q")
                );
                //break;


                //$q = $r->input("q") . "%";
                /*$q = "Over%";
                $model_builder = $model_builder->orWhere(function ($query) use ($model, $field, $r, $q) {
                    $query->where([
                        [(new $model)->getTable() . '.' . $field, "LIKE", $q], //Input::get('q') $r->input("q")
                        ['lang_id', '=', \DB::raw(session('admin_lang_id'))]
                    ]);
                    //->whereRaw();
                });*/
                //if ($first)
                //    $first = 0;
            }
        }

        if ($limit)
            $model_builder = $model_builder->take($limit);
        //echo $r->input("q")."%";
        //echo $model_builder->toSql();

        return $model_builder//->where("name", "like", $r->input("q")."%")
            //->orWhere("lastname", "like", $r->input("q")."%")
            //->select(\DB::raw('CONCAT_WS(" ", firstname, lastname) as fullname, id'))
            ->get();
    }
}