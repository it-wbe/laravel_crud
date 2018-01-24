<?php

namespace Wbe\Crud\Models\Meta;

use Illuminate\Database\Eloquent\Model;
use Wbe\Crud\Models\Translatable;

class MetaDescription extends \Eloquent
{

    public $timestamps = false;
    public $table = "crud_settings_description";

    public function meta(){
        return $this->hasOne('Wbe\Crud\Models\Meta\Meta','id');
    }
}