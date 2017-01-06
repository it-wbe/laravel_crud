<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use View;

class MenuController extends Controller
{
    static public function index()
    {
        /*$menu = DataGrid::source(new TypeContent());
        $menu->attributes(array("class"=>"table"));
        //$menu->add('id','ID', true)->style("width:70px");
        $menu->add('name', 'Content Type', false);
        View::share('menu',compact('menu')['menu']);*/

        $menu = [
            trans('common.to_site') => url('/'),
            trans('common.filemanager') => url('/admin/filemanager/'),
            /*'menu0' => ['menu1' => ['menu2' => ['menu3' => url('/admin/filemanager/')],
                    'menu22' => ['menu33' => url('/admin/filemanager/')]],
                'menu11' => ['menu22' => ['menu33' => url('/admin/filemanager/')]]],*/

            //'Настройки' => url('/admin/settings/'),
            //'Fields Descriptor' => url('/admin/fields_descriptor/'),
            //'t' => ['a'=>'']
        ];
        $content_types = ContentType::orderBy('sort')->get();
        //print_r($content_types);
        foreach ($content_types as $ct) {
            $menu[trans('common.content_types')][$ct->name] = url('admin/crud/grid/' . $ct->id . '/');
        }
        foreach ($content_types as $ct) {
            $menu['<span class="glyphicon glyphicon-cog">'][$ct->name] = [
                    '<span class="glyphicon glyphicon-edit"></span> Дані' =>
                        url('admin/crud/grid/' . $ct->id),
                    '<span class="glyphicon glyphicon-th-list" style="color: #337ab7;"></span> Поля' =>
                        url('admin/fields_descriptor/content/' . $ct->id),
                    '<span class="glyphicon glyphicon-edit"></span> Тип контенту' =>
                        url('admin/crud/edit/3?modify=' . $ct->id . '&to=' . urlencode(url()->full())),
                    //'<span class="glyphicon glyphicon-trash" style="color: #d9534f;"></span> Видалити' =>
                    //    url('admin/crud/delete/' . $ct->id),
                ];
        }

        $menu = self::outputMenu($menu);

        View::share('menu', $menu);
    }

    //, $level = 1
    static public function outputMenu($menu)
    {
        $string = '';
        foreach ($menu as $title => $url) {
            if (!is_array($url)) {
                // style="padding-left:' . $level * 20 . 'px"
                $string .= '<li><a href="' . $url . '">' . $title . '</a></li>';
            } else {
                $string .= '<li class="dropdown">
                 <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $title . ' <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                ';
                /*foreach ($url as $t => $item) {
                    $string .= '<li><a href="' . $item . '" style="padding-left:' . $level * 20 . 'px">' . $t . '</a></li>';
                }*/
                // , ++$level
                $string .= self::outputMenu($url);
                $string .= '</ul></li>';
            }
        }
        return $string;
    }
}
