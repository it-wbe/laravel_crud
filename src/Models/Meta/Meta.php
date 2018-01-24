<?php

namespace Wbe\Crud\Models\Meta;

use Wbe\Crud\CustomClasses\MetaSettings;
use Wbe\Crud\Models\ContentTypes\Languages;
use Illuminate\Database\Eloquent\Model;
use Wbe\Crud\Models\Translatable;

class Meta extends \Eloquent
{
    use Translatable;

    public $timestamps = false;
    public $table = "crud_settings";

        public function description(){
//            return \DB::table('crud_settings_description')->where('content_id','=',$content_id);
            return $this->hasMany('Wbe\Crud\Models\Meta\MetaDescription','content_id');
        }

        public function getDescription(){
            $data = \DB::table('crud_settings_description')->where('content_id','=',$this->id)->get();
            if(count($data)>1) {
                $temp = [];
                $lang = Languages::pluck('id');
                foreach ($lang as $lang) {
                    $temp[$lang] = $data->where('lang_id', '=', $lang)->first();
                }
                return $temp;
            }
            else{
                $temp = (array)$data[0];
                unset($temp['content_id']);
                unset($temp['lang_id']);
                return $temp;
            }
        }
}