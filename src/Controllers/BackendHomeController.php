<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Support\Collection;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\Log\AdminLog;
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
    /**
     * Вивід типів контенту на головній адмінки
     * @param Request $r
     * @return View
     */
    public function index(Request $r)
    {

        if(!empty(\Auth::guard('admin')->user()->settings)){
          $set_temp = collect(unserialize(\Auth::guard('admin')->user()->settings));
          $contents = ContentType::whereIn('id',$set_temp->keys())->get();
        }
        if(!isset($contents)) {
            $content_types = ContentType::where('is_system', '=', 0)->orderBy('sort')->get();
            $contents = $content_types;
        }
        $count = new Collection();
        foreach ($contents as $ctk => $ct) {
                if (\Schema::hasTable($ct->table)) {
                    $count->push(["name" => $ct->name, "count" => \DB::table($ct->table)->count()]);
                }
            }
            $counts = $count->sortByDesc('count');
            $logs = AdminLog::limit(6)->get();

        return view('crud::crud.index', compact('content_types','counts','logs','settings'));
    }


    /**
     * Видалання типу контенту
     * @param Request $r
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function delete(Request $r)
    {
        if (!\Request::has('content_id'))
            return 'no content_id passed';
        $content_id = \Request::input('content_id');
        $content_type = ContentType::where('id', $content_id)->first();
        if ($content_type) {
            //\Schema::dropIfExists($content_type->table);
            //\Schema::dropIfExists($content_type->table . '_description');

            $ct_fields_query = ContentTypeFields::where('content_type_id', $content_id);
            //todo: видаляти _description полів, але потрібно щоб при збереженні описів писались нормальні ІД
            //$ct_fields_query->pluck('id');
            $ct_fields_query->delete();
            //\DB::table('crud_content_type_fields_description')->where('content_id', $content_id)->delete();
            \DB::table('content_type_description')->where('content_id', $content_id)->delete();
            @unlink(ModelGenerator::getModelFilename($content_type->model));
            $content_type->delete();
        } else {
            return 'content type does not exists';
        }
        return redirect()->back();
    }

    /**
     * Отримання та кешування списку мов
     */
    public function language_select()
    {
        if (!Cache::get('languages')) {
            $languages = Cache::remember('languages', 60, function () {
                return \DB::table('languages')->get();
            });
        } else $languages = Cache::get('languages');

        View::share('languages', $languages);
    }

    /**
     * Файловий менеджер
     * @return View
     */
    public function file_manager()
    {
        return view('crud::filemanager');
    }
}
