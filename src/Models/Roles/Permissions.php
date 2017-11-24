<?php
/**
 * Created by PhpStorm.
 * User: bnC-seRVIS
 * Date: 03.11.2017
 * Time: 13:57
 */

namespace Wbe\Crud\Models\Roles;

class Permissions extends \Eloquent
{
    protected $table = 'permissions';

    public $timestamps = false;
    protected $guarded = array();

    public function roles(){
        return $this->belongsToMany('Wbe\Crud\Models\Roles\Role')->withPivot('r','w','d');
    }

}