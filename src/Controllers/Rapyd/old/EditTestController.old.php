<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use App\Models\ContentTypes\Country;
use App\Models\ContentTypes\Teams;



use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataForm\DataForm;


class EditTestController extends Controller
{
    public function index(Request $r)
    {
        //$model = new Teams();
        //if ($r->exists('modify'))
        {

            Teams::saved(function($row){
                echo $row->id;
                print_r($_POST);
                die();
            });

            //$edit = DataForm::source(Teams::find($r->input('modify')));
            $edit = DataForm::source(new Teams);
            $edit->label('Edit Form');
            $edit->link("rapyd-demo/filter", "Articles", "TR")->back();

            //$edit->add('country_id', 'Country', 'text');
            $edit->add('country_id','Country','select')->options(Country::orderBy('name')->pluck('name', 'country_id'));
            $edit->add('description(1).team_name', 'Team Name', 'text');
            $edit->add('description(1).stadium', 'Stadium', 'text');
            $edit->set('description(1).lang_id', 1);
            $edit->add('description(2).team_name', 'Team Name', 'text');
            $edit->add('description(2).stadium', 'Stadium', 'text');
            $edit->set('description(2).lang_id', 2);
            //$edit->add('description.lang_id', '', 'hidden')->insertValue(2);
            $edit->submit('Save');
            $edit->saved(function () use ($edit) {
                $edit->message("record saved");
                $edit->link(url('admin/crud/grid/1/'), "back to the grid");
                //redirect('admin/crud/grid/' . $content_type . '/');
            });

            $return = (string)$edit;

            return view('crud::crud.form', compact('return'));


            /*$edit = DataEdit::source(Hints::find($r->input('modify')));
            $edit->label('Edit Article');
            $edit->link("rapyd-demo/filter","Articles", "TR")->back();
            $edit->add('title','Title', 'text')->rule('required|min:5');

            $edit->add('body','Body', 'redactor');
            $edit->add('detail.note','Note', 'textarea')->attributes(array('rows'=>2));
            $edit->add('detail.note_tags','Note tags', 'text');
            $edit->add('author_id','Author','select')->options(Author::pluck("firstname", "id")->all());
            $edit->add('publication_date','Date','date')->format('d/m/Y', 'it');
            $edit->add('photo','Photo', 'image')->move('uploads/demo/')->fit(240, 160)->preview(120,80);
            $edit->add('public','Public','checkbox');
            $edit->add('categories.name','Categories','tags');

            return view('crud::crud.edit', compact('edit'));*/
        } /*elseif ($r->exists('update')) {
            return 'update';
        } elseif ($r->exists('delete')) {

            return 'delete';
        } else abort(404, 'Action not found');*/
    }
}


