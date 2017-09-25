<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use View;
use Wbe\Crud\Models\ContentTypes\Languages;
use Illuminate\Support\Facades\Route;

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
            $content_types = ContentType::orderBy('sort')->orderBy('is_system')->get();
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


                $menu .= '<li class="treeview ' . $folder_class . '"><a href="#">
                                <span class="glyphicon glyphicon-cog"></span>
                                <span>Field Descriptor</span>    
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
                                '<span>Language Edit</span>'.
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
                    File Manager
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

    private static function generate_lang_menu($menu,$langs){
        switch ($menu){
            case 'crud':
                $path = '/vendor/wbe/crud/lang/';
                break;
            case 'site':
                $path= '/resources/lang/';
                break;
        }

        $submenu_lang = '';
        foreach ($langs as $lang_key => $lang_value){
            $submenu_lang .='<li class="treeview-meny"><a href="#'.$menu.'_lang_'.$lang_key.'">'.
                '<i class="fa fa-language" aria-hidden="true"></i>'.$lang_value.'</a>'.
                '<ul class="treeview-menu">'.VerticalMenuController::generate_lang_menu_item($path.$lang_key.'/',$lang_key).'</ul>'.
                '</li>';
        }
        return $submenu_lang;
    }

    private static function generate_lang_menu_item($path,$lang_key){
        $lang_menu_item = '';
//        $path = '/resources/lang/'.$lang_key.'/';
        //opendir(base_path($path));
//        if(is_dir(base_path($path))) {
            if ($handle = @opendir(base_path($path))) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $entry_arr = explode('.', $entry);
                        $lang_menu_item .= '<li><a href ="' . url('admin/lang_edit?path=' . $path . $entry) . '"><i class="fa fa-file-code-o" aria-hidden="true"></i>' . $entry_arr[0] . '</a></li>';
                    }
                }
                closedir($handle);
            }
//        }
        return $lang_menu_item;
    }
}
