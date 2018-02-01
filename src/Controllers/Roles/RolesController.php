<?php

namespace Wbe\Crud\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;

use Illuminate\Support\Facades\Route;
use Wbe\Crud\Controllers\LangEditController;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\Roles\Permissions;
use Wbe\Crud\Models\Roles\Role as Role;
use Wbe\Crud\Models\ContentTypes\Languages;
use Lang;

class RolesController extends Controller
{
    /**
     * Index page
     *
     * @param Request $r
     * @return View
     */

    public function roleIndex()
    {
        $roles = Role::with('permissions')->where('name','!=','')->get();
        $permissions = $this->getPermissions();
        return view::make('crud::roles.index')->with(['roles' => $roles, 'permissions' => $permissions]);
    }

    public function AddAdminPermissions(){
        $admin = Role::where('name', '=', 'admin')->first();
        $permissions = Permissions::all();//->toArray();
        $admin->permissions()->sync($permissions,true,['pivot_r' => 1,'pivot_w'=>1,'pivot_d'=>1]);
        $admin->save();
        /// set all rights
        foreach ($admin->permissions()->get() as $permission){
            $admin->permissions()->updateExistingPivot($permission->id,['r' => 1,'w'=>1,'d'=>1]);
        }
    }


    protected function getPermissions()
    {
        $temp = Permissions::select()->groupBy('href')->get();
        $permissions['content_type'] = Permissions::where('href', 'like', '%crud/grid%')->get()->toArray();
        $permissions['additional'] = Permissions::where('href', 'like', '%additional%')->get()->toArray();
        $permissions['files'] = [];
        $permissions['files']['site'] = Permissions::where('href', 'like', '%lang_edit/site%')->get()->toArray();
        $permissions['files']['crud'] = Permissions::where('href', 'like', '%lang_edit/crud%')->get()->toArray();
        $permissions['filemanager'] = Permissions::where('href', 'like', '%filemanager%')->get()->toArray();
        $permissions['fields_descriptor'] = Permissions::where('href','like','%fields_descriptor%')->get()->toArray();
        return $permissions;
    }

    public function deleteRole(){
        if(\request()->ajax()){
            $role_id = \request()->get('role_id');
           $role_del =  Role::where('id','=',$role_id)->first();
            $role_del->permissions()->detach($role_del->id);
            $role_del->delete();
            return response(['success'=>'true']);
        }
    }

    public function addRole(){
        if(\request()->ajax()){/// add new role delete all other where no name
            $role_id = \request()->get('role_id');

            // name for new role
            $role_name= \request()->get('role_name');

            $new_role = Role::where('id','=',$role_id)->first();
            $new_role->name = $role_name;
            $new_role->save();

          $empty_roles =   Role::where('name','=','')->get();//->with('permissions')->get();
          foreach ($empty_roles as $role){
              $role->permissions()->detach();
              $role->delete();
          }
            return response(['success'=>'true']);
        }
        $permissions = $this->getPermissions();
        $new_role = Role::create();//->with('permissions');
        $temp[] = $new_role;
        return view('crud::roles.add')->with('roles',$temp)->with('permissions',$permissions);
    }

    public function generatePermissions(){
        $not_system = ContentType::where('is_system','=','0')->get();
        //// not System
        foreach ($not_system as $item_key => $item_value) {
            if (is_null(Permissions::select('href')->where('href', '=', 'admin/crud/grid/' . $item_value->id)->first())) {
                /// add new system type
                $per = new  Permissions(['href' => 'admin/crud/grid/' . $item_value->id, 'name' => $item_value->name]);
                $per->save();
            }
        }

        $system = ContentType::where('is_system','=','1')->get();
        ///// system type
        foreach ($system as $item_key => $item_value) {
            if (is_null(Permissions::select('href')->where('href', '=', 'admin/crud/grid/' . $item_value->id)->first())) {
                /// add new system type
                /// create group system type
                $per = new  Permissions(['href' => 'admin/crud/grid/' . $item_value->id, 'name' => $item_value->name]); // SystemType GROUP
                $per->save();
            }
        }


        /// files
        $langs = Languages::select('name', 'id', 'code')->get()->toArray();
        /// site
        $site_files = $this->get_files('site', $langs);

        foreach ($site_files as $file_name => $file_index) {
            if (is_null(Permissions::select('href')->where('href', '=', 'admin/lang_edit/site/' . $file_name)->first())) {
                $file_name_temp = explode('.', $file_name);
                $per = new  Permissions(['href' => 'admin/lang_edit/site/' . $file_name, 'name' => ucwords($file_name_temp[0])]);
                $per->save();
            }
        }
        /// crud
        $site_files = $this->get_files('crud', $langs);

        foreach ($site_files as $file_name => $file_index) {
            if (is_null(Permissions::select('href')->where('href', '=', 'admin/lang_edit/crud/' . $file_name)->first())) {
                /// create group if not exist
                $file_name_temp = explode('.', $file_name);
                $per = new  Permissions(['href' => 'admin/lang_edit/crud/' . $file_name, 'name' => ucwords($file_name_temp[0])]);
                $per->save();
            }
        }

        // file Manager
        if (is_null(Permissions::select('*')->where('href', '=', 'admin/filemanager')->first())) {
            $per = new  Permissions(['href' => 'admin/filemanager', 'name' => Lang::get('crud::common.file_manager')]);
            $per->save();
        }
        //Aditional
        $routeList = Route::getRoutes();
        foreach ($routeList as $value) {
            preg_match('~additional~', $value->uri(), $additional_page);
//            $val = substr($value->uri(), 0, 5);
//            if ($val=="admin") {
            if (!empty($additional_page[0])) {
                if (is_null(Permissions::select("*")->where('href', '=', $value->uri())->first())) {
                    $temp = explode('/', $value->uri());
                    for ($i = 0; $i <= count($temp); ++$i) {
                        if ($i > 2) {
                            unset($temp[$i]);
                        }
                    }
                    $temps = '';
                    foreach ($temp as $val) {
                        $temps .= $val;
                        if (end($temp) != $val) {
                            $temps .= '/';
                        }
                    }
                    if (!is_null(Permissions::select('*')->where('href', '=', $temps)->first())) {
                        continue;
                    }
                    if ($value->getName()) {
                        $name = $value->getName();
                    } else {
                        $name = $value->uri();
                    }
                    $per = new  Permissions(['href' => $value->uri(), 'name' => $name]);
                    $per->save();

                }
//                dump($value->uri());
            }
        }
//        }
        ///// Field Descriptor
        $content_types = ContentType::select('table', 'id')->get();
        foreach ($content_types as $content_type) {
            if (is_null(Permissions::select("*")->where('href', '=', 'admin/fields_descriptor/content/' . $content_type->id)->first())) {
                $per = new  Permissions(['href' => 'admin/fields_descriptor/content/' . $content_type->id, 'name' => 'field_descriptor_' . $content_type->table]);
                $per->save();
            }
        }
    }



    private function get_files($menu_item, $langs){
        $file_array = [];
        $submenu_lang = '';
        foreach ($langs as $lang_val) {
            $this->generate_lang_menu_item(LangEditController::$langs_folders[$menu_item] . $lang_val['code'] . '/', $menu_item, $file_array);
        }
        return $file_array;
    }

    /**
     *  додавання до масиву файли якщо їх там нема
     * @param $path
     * @param $menu_item
     * @param $file_array
     */
    private function generate_lang_menu_item($path, $menu_item, &$file_array){
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


    public function roleEdit(){
        // role id
        $role_id = \request()->get('role_id');
        // name for new role
        $role_name= \request()->get('role_name');
        /// array [0] - permission id [1] permission rights
        $temp_arr = explode('-', \request()->get('permission_id'));
        $permission_id = $temp_arr[0];


        /// create role or find exist
        if(empty($role_id )){
            $role =  Role::create(['name'=>$role_name]);
            $permission_rights = null;
        }else{
            $permission_rights = Role::find($role_id)->permissions()->where('permissions.id', '=', $permission_id)->first();
        }
        // if isset is content type else some other permission
        // $temp_arr[1] - r/w/d
        if(isset($temp_arr[1])){
            /// for content type permission			
            $permission = $temp_arr[1];
            if ($permission_rights) {
				$temp_message = $this->SetPermission($role_id, $permission_id, $permission, $permission_rights);
				//return response($permission_rights);
				//todo:: !!!! remove permissions
				$changed = Role::find($role_id)->permissions()->where('permissions.id', '=', $permission_id)->where(
				[
				['permissions_role.w','=',0],
				['permissions_role.r','=',0],
				['permissions_role.d','=',0],
				])->first();
				//return response($changed);
				if($changed){
				$this->DelPermission($role_id, $permission_id);
					return response(['del'=>"some"]);
				}else{
					return response(['success' => $temp_message,'perm'=>$permission_rights]);			
				}
				
				
            } else {
                $temp_message =   $this->AddPermission($role_id, $permission_id, $permission);
                return response(['success' => $temp_message]);
            }
        }else{ // for other permission
            if($permission_rights){/// exist permissions need delete
                $this->DelPermission($role_id, $permission_id);
                return response(['permissions'=>'delete','permission_rights'=>$permission_rights]);
            }else{/// not exist need create
                $temp_message =   $this->AddPermission($role_id, $permission_id);
                return response(['permissions'=>"create"]);
            }
        }
    }

    protected function AddPermission($role_id,$permissions_id,$right=null){
        $role = Role::where('id','=',$role_id)->first();
        $permissions = Permissions::where('id','=',$permissions_id)->first();
        if(!is_null($right)){
            $role->permissions()->attach($permissions,[$right=>1]);
        }else{
            $role->permissions()->attach($permissions);
        }
       return $role->save();
    }

    protected function DelPermission($role_id,$permissions_id,$right=null){
        $role = Role::where('id','=',$role_id)->first();
            $role->permissions()->detach($permissions_id);
        return $role->save();
    }


    protected function SetPermission($role_id,$permissions_id,$permission,$permission_rights){
        if($permission_rights->pivot->$permission) {
            $value = 0;
        }else{
            $value = 1;
        }
       return Role::find($role_id)->permissions()->updateExistingPivot($permissions_id,[$permission => $value]);
    }
}
