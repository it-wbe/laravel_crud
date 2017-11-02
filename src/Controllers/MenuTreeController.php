<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\Menus;
use Wbe\Crud\Models\ContentTypes\MenusDescription;
use View;
use Wbe\Crud\Models\ContentTypes\Languages;
use Lang;
class MenuTreeController extends Controller
{
    /**
     * Заповнення масиву з головним меню
     */
    public function index()
    {
        $root = Menus::root();
        $langs = Languages::all()->pluck('name', 'id', 'code');

        if (is_null($root)) {
            $this->tree_generate($langs);
            $root = Menus::root();
        }
//        dd($root->children()->first()->MenusDescriptionLang);
        $root->save();
        $tree = \Zofe\Rapyd\DataTree\DataTree::source($root);
        $tree->add('{{$row->MenusDescriptionLang->title}}');
        $tree->edit(route('menu.editNode'), 'Edit', 'modify|delete');
        $tree->submit('Save');
        return view::make('crud::menutree.index')->with('tree', $tree)->withLangs($langs);
    }

    public function AddCustomNode()
    {

        $root = Menus::root();
        $title = request()->input('title');
        $child1 = $root->children()->create(['href' => "",'item_type'=>MenuTreeController::$item_types['Default_list_item']['id']]);
        foreach ($title as $title_val)
            \DB::table('menus_description')->insert(['content_id' => $child1->id, 'lang_id' => $title_val['lang_id'], 'title' => $title_val['title']]);

        request()->session()->flash('alert-success', 'Node was Added successful !');
        return redirect()->back();
    }

    public function anyMenuedit()
    {
        if (request()->isMethod('post')) {
            if (request()->has('modify')) {
                foreach (request()->get('title') as $desc_val)
                    \DB::table('menus_description')->where([['lang_id', $desc_val['lang_id']], ['content_id', request()->get('modify')]])->update(['title' => $desc_val['title']]);
                request()->session()->flash('alert-success', 'Node was updated successful !');
                return redirect(route('Menu Edit'))->withMessage('log');
            } elseif (request()->has('delete')) {
                \DB::table('menus_description')->where('content_id', '=', request()->get('delete'))->delete();
                \DB::table('menus')->where('id', '=', request()->get('delete'))->delete();
                request()->session()->flash('alert-success', 'Node was removed successful !');
                return redirect(route('Menu Edit'));
            }
        }


//        if (\Input::get('do_delete') == 1) return "not the first";
        if (request()->has('modify')) {
            $langs = Languages::all()->pluck('name', 'id');
            $edit = MenusDescription::where('content_id', '=', request()->get('modify'))->get();
            return view('crud::menutree.edit', compact('edit', 'langs'));
        } elseif (request()->has('delete')) {
            return view('crud::menutree.edit');
        }
    }

    public function tree_generate($regenerate = false)
    {
//        $regenerate = true;
        if ($regenerate) { // delete all menu items with description
            Menus::truncate();
            MenusDescription::truncate();
        }
        $root = Menus::root();
        $langs = Languages::select('name', 'id', 'code')->get()->toArray();
//        if (is_null($root)) {
//            $root = $this->Regenerate($langs);
//        } else {
            $root = $this->AddNew($langs);
//        }
        return redirect()->back();
    }

    private function Regenerate($langs)
    {
        $root = Menus::create(['href' => '','item_type'=>0]);
        foreach ($langs as $lang_val)
            \DB::table("menus_description")->insert(['content_id' => $root->id, 'lang_id' => $lang_val['id'], 'title' => "ROOT"]);

        $all_items_menu = ContentType::select('*')->groupBy('is_system')->get();
        $system_types = null;


        // ContentType
        //Translation Content type

        $description = \DB::table('content_type_description')->get();

        // add Content Types
        foreach ($all_items_menu as $item_key => $item_value) {
            if ($item_value->is_system == 1) {
                if (is_null($system_types)) { // if first here create group

                    $system_types = $root->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["System_Type_Group"]['id']]); // SystemType GROUP
                    foreach ($langs as $lang_val)
                        \DB::table("menus_description")->insert(['content_id' => $system_types->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.systems_types', [], strtolower($lang_val['code']))]);
                }
                // Add System Types
                $temp = $system_types->children()->create(['href' => 'admin/crud/grid/' . $item_value->id, 'item_type' => MenuTreeController::$item_types["Content_SystemType"]['id']]);
                foreach ($langs as $lang_val) {
                    $descr_temp = $description->where('lang_id', $lang_val['id'])->where('content_id', $item_value->id)->first();
                    \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => $descr_temp->name]);
                }
            } // create group just for not system types
            else {
                /// Add Content Types to root
                $temp = $root->children()->create(['href' => 'admin/crud/grid/' . $item_value->id, 'item_type' => MenuTreeController::$item_types["Content_Type"]['id']]);
                foreach ($langs as $lang_val) {
                    $descr_temp = $description->where('lang_id', $lang_val['id'])->where('content_id', $item_value->id)->first();
//                    dd($descr_temp->name);
                    \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => $descr_temp->name]);
                }
            }
        }

        ///// langs add to menu


        // create group Language Edit

        $lang_edit = $root->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Lang_Edit_Group"]['id']]);
        foreach ($langs as $lang_val)
            \DB::table("menus_description")->insert(['content_id' => $lang_edit->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.language_editing', [], strtolower($lang_val['code']))]);

        ///// site lang add

        /// create group Site
        $lang_site = $lang_edit->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Group"]['id']]);
        foreach ($langs as $lang_val)
            \DB::table("menus_description")->insert(['content_id' => $lang_site->id, 'lang_id' => $lang_val['id'], 'title' => 'Site']);

        $site_files = $this->generate_lang_menu('site', $langs);
        foreach ($site_files as $file_name => $file_index) {
            $temp = $lang_site->children()->create(['href' => 'admin/lang_edit/site/' . $file_name, 'item_type' => MenuTreeController::$item_types["Content_SystemType"]['id']]);
            $file_name_temp = explode('.', $file_name);
            foreach ($langs as $lang_val)
                \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => ucwords($file_name_temp[0])]);
        }

        //        /////// crud lang add
        //      create group CRUD
        $lang_site = $lang_edit->children()->create(['href' => '','item_type'=>MenuTreeController::$item_types["Group"]['id']]);
        foreach ($langs as $lang_val)
            \DB::table("menus_description")->insert(['content_id' => $lang_site->id, 'lang_id' => $lang_val['id'], 'title' => 'Crud']);
        $site_files = $this->generate_lang_menu('crud', $langs);
        foreach ($site_files as $file_name => $file_index) {
            $temp = $lang_site->children()->create(['href' => 'admin/lang_edit/crud/' . $file_name,'item_type'=>MenuTreeController::$item_types["Default_list_item"]['id']]);
            $file_name_temp = explode('.', $file_name);
            foreach ($langs as $lang_val)
                \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => ucwords($file_name_temp[0])]);
        }

        //// Add file manager
        $temp = $root->children()->create(['href' => 'admin/filemanager','item_type'=>MenuTreeController::$item_types["FileManager"]['id']]);
        foreach ($langs as $lang_val)
            \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.file_manager', [], strtolower($lang_val['code']))]);
        //add additional menu items
        $routeList = Route::getRoutes();
        foreach ($routeList as $value) {
            preg_match('~additional~', $value->uri(), $additional_page);
            if (!empty($additional_page[0])) {
                $temp = $root->children()->create(['href' => $value->uri(),'item_type'=>MenuTreeController::$item_types["Default_list_item"]['id']]);
                foreach ($langs as $lang_val)
                    \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => $value->getName()]);
            }
        }
    }

    private function AddNew($langs)
    {
        $root = Menus::root();
        if(is_null($root)){
            $root = Menus::create(['href' => '','item_type'=>0]);
        }
        $root = Menus::root();
        $description = \DB::table('content_type_description')->get();
        $all_items_menu = ContentType::all()->groupBy('is_system');

        //// not System
        foreach ($all_items_menu[0] as $item_key => $item_value) {
            if (is_null(Menus::select('href')->where('href', '=', 'admin/crud/grid/' . $item_value->id)->first())) {
                /// add new system type
                $new_type = $root->children()->create(['href' => 'admin/crud/grid/' . $item_value->id,'item_type'=>MenuTreeController::$item_types["Content_Type"]['id']]);
                foreach ($description->where('content_id', $item_value->id) as $lang_val)
                    \DB::table('menus_description')
                        ->insert(['content_id' => $new_type->id, 'lang_id' => $lang_val->lang_id, 'title' => $lang_val->name]);
            }
        }
        ///// system type
        foreach ($all_items_menu[1] as $item_key => $item_value) {
            if (is_null(Menus::select('href')->where('href', '=', 'admin/crud/grid/' . $item_value->id)->first())) {
                /// add new system type
                /// create group system type
                $system_types  =   $this->findNode_ItemType($root,MenuTreeController::$item_types["System_Type_Group"]['id']);
                if(is_null($system_types)){
                    $system_types = $root->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["System_Type_Group"]['id']]); // SystemType GROUP
                    foreach ($langs as $lang_val)
                        \DB::table("menus_description")->insert(['content_id' => $system_types->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.systems_types', [], strtolower($lang_val['code']))]);
                }
                $new_system_type = $system_types->children()->create(['href' => 'admin/crud/grid/' . $item_value->id,'item_type'=>MenuTreeController::$item_types["Content_SystemType"]['id']]);
                foreach ($description->where('content_id', $item_value->id) as $lang_val)
                    \DB::table('menus_description')
                        ->insert(['content_id' => $new_system_type->id, 'lang_id' => $lang_val->lang_id, 'title' => $lang_val->name]);
            }

        }


        /// files
        $lang_edit_group =$this->findNode_ItemType($root,MenuTreeController::$item_types["Lang_Edit_Group"]['id']);
            //// create group Lang Edit if not exist
        if(is_null($lang_edit_group)) {
            $lang_edit_group  = $root->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Lang_Edit_Group"]['id']]);
            foreach ($langs as $lang_val)
                \DB::table("menus_description")->insert(['content_id' => $lang_edit_group->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.language_editing', [], strtolower($lang_val['code']))]);
        }

        /// site
        $site_files = $this->generate_lang_menu('site', $langs);

//        $lang_site_group =$this->findNode_ItemType($root,MenuTreeController::$item_types["Site_group"]['id']);
        /// create group for Site if not exist
//        dump($lang_site_group );
         if(is_null(Menus::select('*')->where('item_type', '=', MenuTreeController::$item_types["Site_group"]['id'])->first())) {
             $lang_site_group = $lang_edit_group->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Site_group"]['id']]);
             foreach ($langs as $lang_val)
                 \DB::table("menus_description")->insert(['content_id' => $lang_site_group->id, 'lang_id' => $lang_val['id'], 'title' => 'Site']);
         }

        foreach ($site_files as $file_name => $file_index) {
            if (is_null(Menus::select('href')->where('href', '=', 'admin/lang_edit/site/' . $file_name)->first())) {
                /// create group if not exist
                $temp = $lang_site_group->children()->create(['href' => 'admin/lang_edit/site/' . $file_name,'item_type'=>MenuTreeController::$item_types["Default_list_item"]['id']]);
                $file_name_temp = explode('.', $file_name);
                foreach ($langs as $lang_val)
                    \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => ucwords($file_name_temp[0])]);
            }
        }
        /// crud
        ///
        ///

        $site_files = $this->generate_lang_menu('crud', $langs);

//        $lang_crud_group  =$this->findNode_ItemType($root,MenuTreeController::$item_types['Crud_group']['id']);
        /// create group for Site if not exist
        ///
        if(is_null(Menus::select('*')->where('item_type', '=', MenuTreeController::$item_types["Crud_group"]['id'])->first())) {
            $lang_crud_group = $lang_edit_group->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Crud_group"]['id']]);
            foreach ($langs as $lang_val)
                \DB::table("menus_description")->insert(['content_id' => $lang_crud_group ->id, 'lang_id' => $lang_val['id'], 'title' => 'Crud']);
        }

        foreach ($site_files as $file_name => $file_index) {
            if (is_null(Menus::select('href')->where('href', '=', 'admin/lang_edit/crud/' . $file_name)->first())) {
                /// create group if not exist
                $temp = $lang_crud_group->children()->create(['href' => 'admin/lang_edit/crud/' . $file_name,'item_type'=>MenuTreeController::$item_types["Default_list_item"]['id']]);
                $file_name_temp = explode('.', $file_name);
                foreach ($langs as $lang_val)
                    \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => ucwords($file_name_temp[0])]);
            }
        }

        // file Manager
        if (is_null(Menus::select('*')->where('item_type', '=', MenuTreeController::$item_types["FileManager"]['id'])->first())) {
            $temp = $root->children()->create(['href' => 'admin/filemanager','item_type'=>MenuTreeController::$item_types["FileManager"]['id']]);
            foreach ($langs as $lang_val)
                \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => Lang::get('crud::common.file_manager', [], strtolower($lang_val['code']))]);

        }

        //Aditional
        $routeList = Route::getRoutes();
        foreach ($routeList as $value) {
            preg_match('~additional~', $value->uri(), $additional_page);
            if (!empty($additional_page[0])) {
                if (is_null(Menus::select('*')->where('href', '=', url($value->uri()))->first())) {
                    $aditional_group =   $this->findNode_ItemType($root,MenuTreeController::$item_types["Additional_group"]['id']);
                      // create grup additional
                      if(is_null($aditional_group)){
                          $aditional_group = $root->children()->create(['href' => '', 'item_type' => MenuTreeController::$item_types["Additional_group"]['id']]);
                          foreach ($langs as $lang_val)
                              \DB::table("menus_description")->insert(['content_id' => $aditional_group->id, 'lang_id' => $lang_val['id'], 'title' => 'Additional']);
                      }
                            $temp = $aditional_group->children()->create(['href' => $value->uri(),'item_type'=>MenuTreeController::$item_types["Additional_item"]['id']]);
                            foreach ($langs as $lang_val)
                                \DB::table("menus_description")->insert(['content_id' => $temp->id, 'lang_id' => $lang_val['id'], 'title' => $value->getName()]);
                }
            }
        }
//        exit;
//        return Menus::root();
    }
    /**
     * генерація пункту меню з списком файлів
     * @param $menu_item 'site' or 'crud'
     * @param $langs   array with id -> language code value -> normal look
     * @return string
     */
    private function generate_lang_menu($menu_item, $langs)
    {
        $file_array = [];
        $submenu_lang = '';
//        dd(LangEditController::$langs_folders[$menu_item]);
        foreach ($langs as $lang_val) {
            MenuTreeController::generate_lang_menu_item(LangEditController::$langs_folders[$menu_item] . $lang_val['code'] . '/', $menu_item, $file_array);
        }
//        foreach ($file_array as $file_name=>$menu_item_val){
//            $entry_arr = explode('.', $file_name);
////            $submenu_lang .= '<li><a href ="' . url('admin/lang_edit/' .$menu_item.'/'.$file_name) . '"><i class="fa fa-file-code-o" aria-hidden="true"></i>' . $entry_arr[0] . '</a></li>';
//        }
        return $file_array;
    }
    /**
     *  додавання до масиву файли якщо їх там нема
     * @param $path
     * @param $menu_item
     * @param $file_array
     */
    private function generate_lang_menu_item($path, $menu_item, &$file_array)
    {
        if ($handle = @opendir(base_path($path))) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (!isset($file_array[$entry])) {
                        $file_array[$entry] = $menu_item;
                    }
                }
            }
            closedir($handle);
        }
    }

    public static $item_types =[
        "Default_list_item"=>['id'=>1,'icon'=>'glyphicon glyphicon-cog'],
        "Content_SystemType" => ['id' => 2, 'icon' => 'glyphicon glyphicon-th'],
        "Content_Type" => ['id' => 3, 'icon' => 'fa fa-link'],
        "FileManager" => ['id' => 4, 'icon' => 'glyphicon glyphicon-floppy-open'],
        "Group" => ['id' => 5, 'icon' => 'glyphicon glyphicon-cog'],
        "Lang_Edit_Group" => ['id' => 6, 'icon' => 'fa fa-language'],
        "System_Type_Group"=>['id'=>7,'icon'=>'glyphicon glyphicon-cog'],
        "Crud_group"=>['id'=>8,'icon'=>'glyphicon glyphicon-cog'],
        "Site_group"=>['id'=>9,'icon'=>'glyphicon glyphicon-cog'],
        "Additional_group"=>['id'=>10,'icon'=>'glyphicon glyphicon-cog'],
        "Additional_item"=>['id'=>11,'icon'=>'fa fa-link'],
    ];

    public static function findType($id){
        foreach (MenuTreeController::$item_types as $item_key=> $type_value){
//            dd($type_value);
            if($type_value['id']==$id){
                return [$item_key=>$type_value];
            }
        }
    }



    /**Find node with needed id_type return first find
     *
     *
     * @param $node // where you whand find
     * @param $item_type // id item type
     */
    public function findNode_ItemType($list , $item_type){
        foreach ($list->children()->get() as $node){
            if(count($node->children()->get())>0) {
                $this->findNode_ItemType($node, $item_type);
                if ($node->item_type == $item_type) {
                    return $node;
                }
            }
        }
    }
}
