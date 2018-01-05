<?php

namespace Wbe\Crud\Models\Log;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends \Eloquent
{
    public $timestamps = false;
    public $table = "admin_log";

    public function content_type()
    {
        return $this->hasOne('Wbe\Crud\Models\ContentTypes\ContentType','id','content_type_id');
    }

    public function user(){
        return $this->hasOne('Wbe\Crud\Models\ContentTypes\User','id','user_id');
    }


}