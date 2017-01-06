<?php

namespace Wbe\Crud\Controllers;

use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ModelGenerator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use Wbe\Crud\Models\ContentTypes\ContentType;
use App\Http\Controllers\Controller;
use Cache;
use View;

class BackendHomeController extends Controller
{
    public function index(Request $r)
    {
        $content_types = ContentType::orderBy('sort')->get();
        foreach ($content_types as $ctk => $ct) {
            if (\Schema::hasTable($ct->table))
                $content_types[$ctk]->records_count = \DB::table($ct->table)->count();
            else
                $content_types[$ctk]->records_count = '<span style="color:red">table not found</span>';
            $content_types[$ctk]->descripted_fileds = \DB::table('crud_content_type_fields')->where('crud_content_type_id', $ct->id)->count();
        }
        return view('crud::crud.fd_content_types', compact('content_types'));
    }

    public function delete(Request $r)
    {
        if (!\Request::has('content_id'))
            return 'no content_id passed';
        $content_id = \Request::input('content_id');
        $content_type = ContentType::where('id', $content_id)->first();
        if ($content_type) {
            //\Schema::dropIfExists($content_type->table);
            //\Schema::dropIfExists($content_type->table . '_description');

            $ct_fields_query = ContentTypeFields::where('crud_content_type_id', $content_id);
            //todo: видаляти _description полів, але потрібно щоб при збереженні описів писались нормальні ІД
            //$ct_fields_query->pluck('id');
            $ct_fields_query->delete();
            //\DB::table('crud_content_type_fields_description')->where('content_id', $content_id)->delete();
            \DB::table('crud_content_type_description')->where('content_id', $content_id)->delete();
            @unlink(ModelGenerator::getModelFilename($content_type->model));
            $content_type->delete();
        } else {
            return 'content type does not exists';
        }
        return redirect()->back();
    }



    public function language_select()
    {
        if (!Cache::get('languages')) {
            $languages = Cache::remember('languages', 60, function () {
                return \DB::table('languages')->get();
            });
        } else $languages = Cache::get('languages');

        View::share('languages', $languages);
    }

    public function file_manager()
    {
        return view('crud::filemanager');
    }
}
