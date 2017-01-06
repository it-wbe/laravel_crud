<?php

namespace Wbe\Crud\Models\ContentTypes;

use Illuminate\Database\Eloquent\Model;

class Languages extends \Eloquent
{
    public $timestamps = false;

    /*static public function getCurrent()
    {
        return session((is_admin_panel() ? 'admin_lang_id' : 'lang_id'));
    }*/
}