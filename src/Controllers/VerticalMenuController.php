<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use View;

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
            $content_types = ContentType::orderBy('sort')->get();
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
            }

            $menu .= '<li>
                <a href="' . url('admin/filemanager/') . '">
                    <i class="glyphicon glyphicon-floppy-open"></i>
                    File Manager
                </a>
            </li>';
        }


        View::share('vertical_menu', $menu);
    }
}
