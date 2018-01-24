<?php
namespace  Wbe\Crud\CustomClasses;

use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\Globals;
use Wbe\Crud\Models\Meta\Meta;
use  Illuminate\Database\Schema\Blueprint;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;

class MetaSettings
{

    public static $columns=['meta_title','meta_description'];
    private static $table_name_meta = 'crud_settings';
    /**
     * Check if need comments
     * @param $content_type_id
     * @return bool
     */
    public static function NeedMeta($content_type_id){
//        dd(\DB::table('content_type')->where('id','=',$content_type_id)->pluck('need_meta')->first());
        if(\DB::table('content_type')->where('id','=',$content_type_id)->pluck('need_meta')->first()){
            return true;
        }
        else{
            return false;
        }
    }


    /**
     * Add meta columns to table
     *
     * @param $table_name Content_Type
     */
    public function AddMetaFieldsTo($content){
        try{
        if(MetaSettings::NeedMeta($content->id) && $content->is_system ==0){
            $created = false;
            if(MetaSettings::is_description_table($content->table)){
                // add columns to description table
               $created =  $this->columns_create($content->table.'_description');
            }else{
                // add columns to data table
               $created =  $this->columns_create($content->table);
            }
            if($created){
                $settings =  MetaSettings::get_settings_meta($content->id);
                if($settings){
                    return $settings;
                }else{
                    $settings =[];
                    /// create new obj
                    foreach (MetaSettings::$columns as $col){
                        $settings[$col] = "";
                    }
                    return $settings;
                }
            }else{
                return false;
            }
        }else{
            // delete meta if exist
            $this->columns_delete($content->table);
            return false;
        }
        }catch (\Exception $ex){
            Globals::$messages[2][] = $ex->getMessage();
        }
        return false;
    }


    /**
     * Chech exist description table
     *
     * @param $table_name table name data
     * @return bool if exist - true  if NOT exist false
     */
    public static function is_description_table($table_name){
        $table_name_desc = $table_name."_description";
        if(\Schema::hasTable($table_name_desc)){
            return true;
        }else{
            return false;
        }
    }

    public function add(array $data,$table_name){
        $add =  Meta::where(['content_type_id'=>$data['content_type_id']])->first();
        if(empty($add)){
            $add = new Meta();
            $add->content_type_id  = $data['content_type_id'];
            $add->save();
        }
        $langs = Languages::pluck('id');
        $temp = \DB::table(MetaSettings::$table_name_meta.'_description')->where('content_id','=',$add->id)->get();
        if($this->is_description_table($table_name)){ /// for multi lang
            if(empty(count($temp))){/// insert
                foreach ($langs as $lang_id){
                    \DB::table(MetaSettings::$table_name_meta.'_description')
                        ->insert(['content_id'=>$add->id,'lang_id'=>$lang_id,'meta_title'=>$data['meta_title'][$lang_id],'meta_description'=>$data['meta_description'][$lang_id]]);
                }
            }else{ // update
                foreach ($langs as $lang_id){
                    \DB::table(MetaSettings::$table_name_meta.'_description')->where([['content_id','=',$add->id],['lang_id','=',$lang_id]])
                        ->update(['meta_title'=>$data['meta_title'][$lang_id],'meta_description'=>$data['meta_description'][$lang_id]]);
                }
            }
        }else{// one lang
           if(empty(count($temp))){// insert
               \DB::table(MetaSettings::$table_name_meta.'_description')
                   ->insert(['content_id'=>$add->id,'lang_id'=>'"-1"','meta_title'=>$data['meta_title'][0],'meta_description'=>$data['meta_description'][0]]);
           }else{// update
               \DB::table(MetaSettings::$table_name_meta.'_description')->where([['content_id','=',$add->id],['lang_id','=','-1']])
                   ->update(['meta_title'=>$data['meta_title'][0],'meta_description'=>$data['meta_description'][0]]);
           }
        }
    }

    private function columns_create($table_name){
        if(\Schema::hasTable($table_name)){
        foreach (MetaSettings::$columns as $column){
            if(!\Schema::hasColumn($table_name, $column)){
                \Schema::table($table_name, function (Blueprint $table)use ($column)
                {
                    $table->text($column);
                });
            }
        }
            return true;
        }else{
            throw new \Exception("not table ".$table_name);
        }
    }

    private function columns_delete($table_name){
        if(\Schema::hasTable($table_name)){
            foreach (MetaSettings::$columns as $column){
                if(\Schema::hasColumn($table_name, $column)){
                    \Schema::table($table_name, function (Blueprint $table,$column)
                    {
                        $table->dropColumn($column);
                    });
                }
            }
            return true;
        }else{
            throw new \Exception("not table ".$table_name);
        }
    }


    /**
     * Generate MetaTage
     *
     * @param $content_type_id - content type id
     * @param $lang_id  - curent lang - if need description require
     * @param null $data_description - new description_data
     */
    public static function GenerateMeta($content,$all_data=null,$lang_id=null,$data_description=null){
        if(!MetaSettings::NeedMeta($content->id)){
            if(!is_null($data_description)){
				return $data_description;
			}else{
				return $all_data;
			}
        }

        $settings = MetaSettings::get_settings_meta($content->id);
        if($lang_id){/// for description
            if(!MetaSettings::is_description_table($content->table)){
//                dd('return row in if');
                return $data_description;
            }
              $description_settings =   $settings->getDescription();
              foreach(MetaSettings::$columns as $col){
                    if(is_null($data_description[$col])) {
                        $data_description[$col] = MetaSettings::replace($col,$description_settings[$lang_id]->$col,$data_description);
                    }
              }
//            dd('return all');
              return $data_description;
        }else{/// for data
            if(MetaSettings::is_description_table($content->table)){
               return $all_data;
            }
//            dd('for data');
            $data_settings = $settings->getDescription();
            foreach(MetaSettings::$columns as $col){
                if(is_null($all_data[$col])) {
                     $all_data[$col] = MetaSettings::replace($col,$data_settings[$col],$all_data);
//                    dump($col,$all_data[$col]);
                }
            }
//            dd($all_data['meta_title']);
            return $all_data;
        }

    }

    private static function replace($col,$settings,$data){
        preg_match_all("/\[.*?\]/",$settings,$result_arr);
        $temp = $settings;
        foreach($result_arr[0] as $setting){
            $replace = $data[substr($setting,1,-1)];
            $temp = str_replace($setting,$replace,$temp);
        }
        return $temp;
    }

    public static function  set_meta_to_form($content,$filds){
//        dd($content);
        if(MetaSettings::NeedMeta($content->id)) {
            $colection = [];
            foreach (MetaSettings::$columns as $col) {
                $colection[$col] = new ContentTypeFields;
                $colection[$col]->type = "text";
                $colection[$col]->name = $col;
                $colection[$col]->title = $col;
                $filds->put($col, $colection[$col]);
            }
        }
    }

    public static function get_settings_meta($content_type_id){
       $meta =Meta::where('content_type_id','=',$content_type_id)->first();
        return $meta;
    }
}