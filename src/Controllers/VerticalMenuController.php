<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use View;
use Wbe\Crud\Models\ContentTypes\Languages;
use Illuminate\Support\Facades\Route;
use Lang;
use Wbe\Crud\Models\ContentTypes\Menus;
class VerticalMenuController extends Controller
{

//    for add new category vartical menu add this category to checkMiddleware swich


    /**
     * Заповнення масиву з головним меню
     */
    static public function index()
    {
        $menu = '';
        $root = Menus::root();
        $url_array = explode('/',url()->current());
        $system_types = \DB::table('content_type')->where('is_system','=',1)->pluck('id')->toArray();
        $menu.=  VerticalMenuController::ShowList($root,$url_array,$system_types);
        View::share('vertical_menu', $menu);

    }



    static  protected function ShowGroup($node,$url_array,$system_types){
     $submenu = '';
            $submenu .="<li class='treeview ".VerticalMenuController::active($node,$url_array,$system_types)."' >".
                "<a href='#'>".
                "<span class='  ".$node->icon."'></span>".
                $node->MenusDescriptionLang->title.
                '<span class="pull-right-container">'.
                '<i class="fa fa-angle-left pull-right"></i>'.
                '</span>'.
                "</a>".
                "<ul class='treeview-menu'> ";
            $submenu.= VerticalMenuController::ShowList($node,$url_array,$system_types);
            $submenu.='</ul></li>';
            return "<li class='treeview'>".$submenu."</li>";

    }

    static protected function ShowList($node,$url_array,$system_types){
        $submenu='';
        foreach ($node->children()->get() as $node) {
            if (VerticalMenuController::checkMiddleware($node)) {
                if (count($node->children()->get()) > 0) {
                    $submenu .= VerticalMenuController::ShowGroup($node,$url_array,$system_types);
                } else {
                    if($node->item_type !=12 &&$node->item_type !=13) {
//                    $type_id = key(MenuTreeController::findType($node['item_type']));
						if(!is_null($node->href)){
						$url = url($node->href);
						}else{
							$url=$node->href;
						}
                        $submenu .= '<li class="' . VerticalMenuController::active($node, $url_array, $system_types) . '">' .
                            '<a href="' . $url . '">' .
                            '<i class="' . $node->icon . '"></i>' .
                            $node->MenusDescriptionLang->title .
                            '</a>' .
                            '</li>';
                    }
                    if($node->item_type==12){
                        $submenu .= '<li class="menu-label">' .
                            $node->MenusDescriptionLang->title .
                            '</li>';
                    }
                    if($node->item_type==13){
                        $submenu .= '<li class="menu-delimiter"></li>';
                    }
                }

            }
        }
        return $submenu;
    }

    static protected function checkMiddleware($node)
    {
        switch ($node->item_type) {
            case MenuTreeController::$item_types["System_Type_Group"]['id']: // content type system group
                if(self::permission_group($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Content_SystemType"]['id']: // content type system item
                if(self::permission_node($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Content_Type"]['id']: //// content_type item
                if(self::permission_node($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Lang_Edit_Group"]['id']: /// Lang file edit  all groups
                if(self::permission_group($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Crud_group"]['id']:  // lang group  crud filse
                if(self::permission_group($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Site_group"]['id']:  // lang group site filse
                if(self::permission_group($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Additional_group"]['id']: // aditional group
                if(self::permission_group($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Additional_item"]['id']:  // aditional item list
                if(self::permission_node($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["Group"]['id']: //// some custome group
                return true;
                break;
            case MenuTreeController::$item_types["Default_list_item"]['id']: // some custome item
                return true;
                break;
            case MenuTreeController::$item_types["FileManager"]['id']: // File manager
                if(self::permission_node($node)){
                    return true;
                }
                return false;
                break;
            case MenuTreeController::$item_types["label"]['id']: // label
                return true;
                break;
            case MenuTreeController::$item_types["delimeter"]['id']: // label
                return true;
                break;
            //// ROOT
            case 0:
                return true;
                break;
        }
        return false;
    }

   static private function permission_group($node){
       foreach ($node->getDescendants() as $menu){
               if(self::permission_node($menu)){
            return true;
           }
       }
    }
    static private function permission_node($node){
        if(!is_null($node->href)) {
            if (\Auth::guard('admin')->user()->role->HasPermission($node->href)) {
                return true;
            }
        }
    }

    protected static function active($node,$url_array,$system_types){
       $href_arr = explode('/',$node->href);
        switch ($node['item_type']) {
            case MenuTreeController::$item_types["System_Type_Group"]['id']: // content type system group
                if(isset($url_array[4])&&$url_array[4]=='crud'){
                    if(in_array(end($url_array),$system_types))
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Content_SystemType"]['id']: // content type system item
                if(isset($url_array[4])&&$url_array[4]=='crud'&&end($href_arr) == end($url_array)){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Content_Type"]['id']: //// content_type item
                if(isset($url_array[4])&&$url_array[4]=='crud'&&end($href_arr) == end($url_array)){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Lang_Edit_Group"]['id']: /// Lang file edit  all groups
                if(isset($url_array[4])&&$url_array[4]=='lang_edit'){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Crud_group"]['id']:  // lang group  crud filse
                if(isset($url_array[5])&&$url_array[5]=='crud'){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Site_group"]['id']:  // lang group site filse
                if(isset($url_array[5])&&$url_array[5]=='site'){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Additional_group"]['id']: // aditional group
                if(isset($url_array[4])&&$url_array[4]=='additional'){
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["Additional_item"]['id']:  // aditional item list
                if(isset($url_array[4])&&$url_array[4]=='additional'&&end($href_arr) == end($url_array)) {
                    return 'active';
                }
                break;
//            case MenuTreeController::$item_types["Group"]['id']: //// some custome group
//                return '';
//                break;
            case MenuTreeController::$item_types["Default_list_item"]['id']: // some custome item
                if(end($href_arr) == end($url_array)) {
                    return 'active';
                }
                break;
            case MenuTreeController::$item_types["FileManager"]['id']: // File manager
                if(isset($url_array[4])&&$url_array[4]=='filemanager') {
                    return 'active';
                }
                break;
            //// ROOT
            case 0:
                return 'active';
                break;
        }
        return '';
    }

//"Default_list_item"=>['id'=>1,'icon'=>'glyphicon glyphicon-cog'],
//"Content_SystemType" => ['id' => 2, 'icon' => 'glyphicon glyphicon-th'],
//"Content_Type" => ['id' => 3, 'icon' => 'fa fa-link'],
//"FileManager" => ['id' => 4, 'icon' => 'glyphicon glyphicon-floppy-open'],
//"Group" => ['id' => 5, 'icon' => 'glyphicon glyphicon-cog'],
//"Lang_Edit_Group" => ['id' => 6, 'icon' => 'fa fa-language'],
//"System_Type_Group"=>['id'=>7,'icon'=>'glyphicon glyphicon-cog'],
//"Crud_group"=>['id'=>8,'icon'=>'glyphicon glyphicon-cog'],
//"Site_group"=>['id'=>9,'icon'=>'glyphicon glyphicon-cog'],
//"Additional_group"=>['id'=>10,'icon'=>'glyphicon glyphicon-cog'],
//"Additional_item"=>['id'=>11,'icon'=>'fa fa-link'],
//"lable"=>['id'=>12,'icon'=>''],
//"delimetr"=>['id'=>13,'icon'=>''],

}
