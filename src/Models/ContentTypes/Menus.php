<?php
/**
 * Created by PhpStorm.
 * User: bnC-seRVIS
 * Date: 18.08.2017
 * Time: 16:32
 */

namespace Wbe\Crud\Models\ContentTypes;

use Baum\Node;
use Wbe\Crud\Models\ContentTypes\Languages;
use App;

class Menus extends Node
{

    public $timestamps = false;

    protected $table = 'menus';

    // 'parent_id' column name
    protected $parentColumn = 'parent_id';

    // 'lft' column name
    protected $leftColumn = 'lft';

    // 'rgt' column name
    protected $rightColumn = 'rgt';

    // 'depth' column name
    protected $depthColumn = 'depth';

    // guard attributes from mass-assignment
    protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

    public function MenusDescription(){
       return $this->hasMany('Wbe\Crud\Models\ContentTypes\MenusDescription','content_id');
    }

    public function MenusDescriptionLang(){
        $lang_id = null;
        if(!is_null(\Lang::locale())){
            $lang_id = Languages::select('id')->where('code','=',\Lang::locale())->first();
        }
        else{
            $lang_id = Languages::select('id')->where('code','=','en')->first();
        }
       $a =  $this->hasOne('Wbe\Crud\Models\ContentTypes\MenusDescription','content_id')
           ->where('lang_id','=',$lang_id->id);
        return $a;
    }
}