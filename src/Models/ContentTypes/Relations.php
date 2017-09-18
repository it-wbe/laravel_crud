<?php
namespace Wbe\Crud\Models\ContentTypes;

/*
 *  Model for content type Images
 *  [leave this text to regenerate entire model]
 */

class Relations extends \Eloquent
{
    use \Wbe\Crud\Models\Translatable;
    protected $table = 'relations';
    public $timestamps = false;
    protected $guarded = array();

    public function portfolios()
    {
        return $this->morphedByMany('App\Models\Portfolios', 'ct_to_relations');
    }
}
