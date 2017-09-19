<?php

namespace Wbe\Crud\Models\ContentTypes;

/*
 *  Model for content type Relations
 *  [leave this text to regenerate entire model]
 */

class Relations extends \Eloquent
{
    use \Wbe\Crud\Models\Translatable;
    protected $table = 'relations';
    public $timestamps = false;
    protected $guarded = array();

    public function posts()
    {
        return $this->morphedByMany('App\Models\Posts', 'ct_to_relations');
    }
}