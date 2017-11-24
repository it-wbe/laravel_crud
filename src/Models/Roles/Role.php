<?php
/**
 * Created by PhpStorm.
 * User: bnC-seRVIS
 * Date: 18.08.2017
 * Time: 16:32
 */

namespace Wbe\Crud\Models\Roles;


class Role extends \Eloquent
{

    public $timestamps = false;

    public function user(){
        return $this->hasOneMany('Wbe\Crud\Models\ContentTypes\User','role_id');
    }

    public function  permissions(){
        return $this->belongsToMany('Wbe\Crud\Models\Roles\Permissions','permissions_role')->withPivot('r','w','d');
    }

    public function allPermissions(){
        return $this->permissions()->select('name','href')->get();
    }

    /**
     * true if has Permissions else false
     *
     * @param $href
     */
    public function HasPermission($href,$rights=null){

        $temp = $this->permissions()->where('href','=',$href)->first();
        if(is_null($temp)){
            return false;
        }
        else {
            if(is_null($rights)){
                return true;
            }elseif($temp->pivot[$rights]){
                return true;
            }
            else{
                return false;
            }
        }
    }
}