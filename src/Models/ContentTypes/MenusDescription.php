<?php
/**
 * Created by PhpStorm.
 * User: bnC-seRVIS
 * Date: 18.08.2017
 * Time: 16:32
 */

namespace Wbe\Crud\Models\ContentTypes;

use Illuminate\Database\Eloquent\Model;

class MenusDescription extends \Eloquent
{

    public $timestamps = false;

    protected $table = 'menus_description';
    protected $primaryKey = 'content_id';

    public function Menu(){
      return  $this->belongsToMany('Wbe\Crud\Models\ContentTypes\Menus','id');
    }
}