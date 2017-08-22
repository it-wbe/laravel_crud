<?php
/**
 * Created by PhpStorm.
 * User: bnC-seRVIS
 * Date: 18.08.2017
 * Time: 16:32
 */

namespace Wbe\Crud\Models\ContentTypes;


class Role extends \Eloquent
{

    public $timestamps = false;

    public function user(){
        return $this->hasOneMany('Wbe\Crud\Models\ContentTypes\Roles\User','role_id');
    }

}