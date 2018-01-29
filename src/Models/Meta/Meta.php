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
            return $this->hasMany('Wbe\Crud\Models\Meta\MetaDescription','content_id');
        }

        public function getDescription($table = null){
            $data = \DB::table('crud_settings_description')->where('content_id','=',$this->id)->get();
            if(MetaSettings::is_description_table($table)) {
                $langs = Languages::pluck('id');
                if($data->count()==0){
                    $temp = [];
                    foreach ($langs as $lang){
                        $temp[$lang] = [];
                        foreach (MetaSettings::$columns as $col){
                            $temp[$lang][$col]="";
                        }
                    }
                    return $temp;
                }else{
                    $temp = [];
                    foreach ($langs as $lang) {
                        $temp[$lang] = $data->where('lang_id', '=', $lang)->first();
                    }
                    return $temp;
                }
            }
            else{
                if($data->count()==0){
                    $settings =[];
                    foreach (MetaSettings::$columns as $col){
                        $settings[$col] = "";
                    }
                    return $settings;
                }else{
                    return $data[0];
                }


            }
        }
}