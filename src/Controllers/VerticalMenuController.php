<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use View;
use Wbe\Crud\Models\ContentTypes\Languages;
use Illuminate\Support\Facades\Route;
use Lang;

class VerticalMenuController extends Controller
{
    /**
     * Заповнення масиву з головним меню
     */
    static public function index()
    {
        $menu = '';
        if (\Auth::guard('admin')->check()) {
//            $menu = [
//                trans('crud::common.to_site') => url('/'),
//                trans('crud::common.filemanager') => url('/admin/filemanager/'),
//            ];
            $content_types = ContentType::where('is_system','=',0)->orderBy('sort')->get();
            foreach ($content_types as $content_type) {

                if(url('admin/crud/grid/' . $content_type->id . '/') == url()->current()) $class = 'active';
                else $class = '';

                if (\Gate::forUser(\Auth::guard('admin')->user())->allows('edit-crud-system-content-type', $content_type->id) || \Gate::forUser(\Auth::guard('admin')->user())->allows('access-content-type', $content_type->id)) {
                    $menu .= '<li class="' . $class . '">
                        <a href="' . url('admin/crud/grid/' . $content_type->id . '/') . '">
                            <i class="fa fa-link"></i>' .
                        $content_type->name
                        . '</a>
                    </li>';
                }
            }

            if(\Gate::forUser(\Auth::guard('admin')->user())->allows('access-field-descriptor')) {
                $submenu = '';
                $folder_class = '';
                foreach ($content_types as $content_type) {

                    if(url('admin/fields_descriptor/content/' . $content_type->id . '/') == url()->current()) {
                        $class = 'active';
                        $folder_class = 'active';
                    } else {
                        $class = '';
                    }

                    if (\Gate::forUser(\Auth::guard('admin')->user())->allows('edit-crud-system-content-type', $content_type->id)) {
                        $submenu .= '<li class="' . $class . '">
                            <a href="' . url('admin/fields_descriptor/content/' . $content_type->id . '/') . '">
                                <i class="glyphicon glyphicon-th"></i>' .
                            $content_type->name
                            . '</a>
                        </li>';


                    }
                }
//                 site path to lang
                //        $path = '/resources/lang/'.$lang_key.'/';
//                 crud path to lang
                //          $path = '/vendor/crud/lang/'.$lang_key.'/';
                $langs = Languages::all()->pluck('name', 'code');
                $submenu_lang = '';
                $submenu_lang .= '<li class="treeview-meny"><a href="#site">'.
                    '<i class="fa fa-language" aria-hidden="true"></i>SITE</a>'.
                    '<ul class="treeview-menu">'.VerticalMenuController::generate_lang_menu('site',$langs).'</ul>'.
                    '</li>';
                $submenu_lang .= '<li class="treeview-meny"><a href="#crud">'.
                    '<i class="fa fa-language" aria-hidden="true"></i>CRUD</a>'.
                    '<ul class="treeview-menu">'.VerticalMenuController::generate_lang_menu('crud',$langs).'</ul>'.
                    '</li>';
                //////////////////////////   SYSTEM TYPES   ////////////////////////////////
                ///
                $content_types = ContentType::where('is_system','=',1)->orderBy('sort')->get();
                $submenu_system_type = '';
                foreach ($content_types as $content_type) {

                    if(url('admin/crud/grid/' . $content_type->id . '/') == url()->current()) $class = 'active';
                    else $class = '';

                    if (\Gate::forUser(\Auth::guard('admin')->user())->allows('edit-crud-system-content-type', $content_type->id) || \Gate::forUser(\Auth::guard('admin')->user())->allows('access-content-type', $content_type->id)) {
                        $submenu_system_type .= '<li class="' . $class . '">
                        <a href="' . url('admin/crud/grid/' . $content_type->id . '/') . '">
                            <i class="fa fa-link"></i>' .
                            $content_type->name
                            . '</a>
                    </li>';
                    }
                }
                $menu.='<li class="treeview ' . $folder_class . '"><a href="#">'.
                    '<span class="glyphicon glyphicon-cog"></span>'.
                    '<span>'. Lang::get('crud::common.systems_types').'</span>'.
                '<span class="pull-right-container">'.
                                    '<i class="fa fa-angle-left pull-right"></i>'.
                '</span>'.
                          '</a>'.
                '<ul class="treeview-menu">'.
                    $submenu_system_type.
                '</ul>'.
                '</li>';
                //////////////////////////   FIELD DESCRIPTOR   ////////////////////////////////

                $menu .= '<li class="treeview ' . $folder_class . '"><a href="#">
                                <span class="glyphicon glyphicon-cog"></span>
                                <span>'.Lang::get('crud::common.fields_descriptors').'</span>    
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                          </a>';

                $menu .= '<ul class="treeview-menu">';
                $menu .= $submenu;
                $menu .= '</ul>';
                $menu .= '</li>';
                /////// language edit menu
                $menu .='<li class="treeview"><a href="menu_lang">'.
                                '<span class="glyphicon glyphicon-cog"></span>'.
                                '<span>'.Lang::get('crud::common.language_editing').'</span>'.
                                '<span class="pull-right-container">'.
                                '<i class="fa fa-angle-left pull-right"></i>'.
                                '</span>'.
                          '</a>';
                $menu .= '<ul class="treeview-menu">';
                $menu .= $submenu_lang;
                $menu .= '</ul>';
                $menu .= '</li>';

            }

            $menu .= '<li>
                <a href="' . url('admin/filemanager/') . '">
                    <i class="glyphicon glyphicon-floppy-open"></i>
                    '.Lang::get('crud::common.file_manager').'
                </a>
            </li>';


            $routeList = Route::getRoutes();

            foreach ($routeList as $value)
            {
                preg_match('~additional~', $value->uri(), $additional_page);
                if(!empty($additional_page[0])) {
                    $menu .= '<li>
                       <a href="' . url($value->uri()) . '">
                           <i class="glyphicon glyphicon-file-open"></i>
                           ' .  $value->getName() . '
                       </a>
                   </li>';
                }
            }

        }
        View::share('vertical_menu', $menu);
    }

    /**
     * генерація пункту меню з списком файлів
     * @param $menu_item   'site' or 'crud'
     * @param $langs   array with id -> language code value -> normal look
     * @return string
     */
    private static function generate_lang_menu($menu_item,$langs){
        $file_array = [];
        $submenu_lang = '';
        foreach ($langs as $lang_key => $lang_value){
            VerticalMenuController::generate_lang_menu_item(LangEditController::$langs_folders[$menu_item].$lang_key.'/',$menu_item,$file_array);
        }
        foreach ($file_array as $file_name=>$menu_item_val){
            $entry_arr = explode('.', $file_name);
            $submenu_lang .= '<li><a href ="' . url('admin/lang_edit/' .$menu_item.'/'.$file_name) . '"><i class="fa fa-file-code-o" aria-hidden="true"></i>' . $entry_arr[0] . '</a></li>';
        }
        return $submenu_lang;
    }


    /**
     *  додавання до масиву файли якщо їх там нема
     * @param $path
     * @param $menu_item
     * @param $file_array
     */
    private static function generate_lang_menu_item($path,$menu_item,&$file_array){

            if ($handle = @opendir(base_path($path))) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        if(!isset($file_array[$entry])){
                            $file_array[$entry] = $menu_item;
                        }
                    }
                }
                closedir($handle);
            }
    }
}
