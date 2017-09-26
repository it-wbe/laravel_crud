<?php

namespace Wbe\Crud\Controllers;

use Mockery\Exception;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ModelGenerator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use Wbe\Crud\Models\ContentTypes\ContentType;
use App\Http\Controllers\Controller;
use Cache;
use View;

class LangEditController extends Controller
{

    public static $langs_folders = ['crud'=>'/vendor/wbe/crud/lang/','site'=>'/resources/lang/'];

    /**
     * Редагування мовних файлів
     * @param Request $r
     * @return View
     */
    public function edit(Request $r,$menu_item,$file_name)
    {
        if(!isset(LangEditController::$langs_folders[$menu_item])){
            return redirect(404);
        }
        $langs = Languages::all()->pluck('code','name')->all();
        if($r->isMethod('post')){
            /// save value to file
            $context_val = \Request::all();
            $put_error = false;
            unset($context_val['_token']);
            foreach ($langs as $lang_code=>$lang_val)
            {
                $context ='';
                $context .='<?php '.PHP_EOL;
                $context .='return ['.PHP_EOL;
                if(isset($context_val[$lang_val])){
                    $this->GetValue($context_val[$lang_val],$context);
                    $context .='];';
                    $put_error[$lang_val]  = file_put_contents(base_path(LangEditController::$langs_folders[$menu_item].$lang_val.'/'.$file_name),$context);
                }
                else{
                    /// такої мови не існує в мовних файлах
                    $put_error[$lang_val] = false;
                }
            }

            foreach ($langs as $lang_code=>$lang_val) {
                if ($put_error[$lang_val]) {
//                    $r->session()->flash('alert-success', 'Language file '.$lang_code.' successful saved!');
                } else {
                    $r->session()->flash('alert-danger', 'Something go wrong! with '.$lang_code);
                }
            }
            return redirect()->back();

        }else{///// GET method

            $lang_array = [];
            foreach ($langs as $lang_code){
                /// папка з мовою не ыцснують копіюємо
                if(!file_exists(base_path(LangEditController::$langs_folders[$menu_item].$lang_code))){
                    try {
//                        якщо є англ версія то копіюємо її
                        if (file_exists(base_path(LangEditController::$langs_folders[$menu_item] . 'uk'))) {
                            $this->recurse_copy(base_path(LangEditController::$langs_folders[$menu_item] . '/en') , base_path(LangEditController::$langs_folders[$menu_item] . $lang_code));
                        }
                        else{
//                            беремо першу мовну папку та копіюємо її
                            $d = dir(base_path(LangEditController::$langs_folders[$menu_item]));
//                            echo "Handle: " . $d->handle . "\n";
//                            echo "Path: " . $d->path . "\n";
                            while (false !== ($entry = $d->read())) {
                                if($entry!='.'&&$entry!='..'){
                                    $first_dir_lang = LangEditController::$langs_folders[$menu_item].$entry;
//                                    dd($first_dir_lang );
                                    break;
                                }
                            }
                            $d->close();
                            /// копіюємо
                            $this->recurse_copy(base_path($first_dir_lang) , base_path(LangEditController::$langs_folders[$menu_item] . $lang_code));

                        }
                    }
                    catch (Exception $ex){
//                        dd($ex);
                    }
                }
                if(!file_exists(base_path(LangEditController::$langs_folders[$menu_item].$lang_code.'/'.$file_name))){
                    foreach ($langs as $lang_code_cur=>$lang_val_cur){
                        if(file_exists(base_path(LangEditController::$langs_folders[$menu_item].$lang_code_cur.'/'.$file_name))){
                            copy(base_path(LangEditController::$langs_folders[$menu_item].$lang_code_cur.'/'.$file_name,base_path(LangEditController::$langs_folders[$menu_item].$lang_code.'/'.$file_name)));
                            break;
                        }
                    }
                }

                $lang_array[$lang_code] = @include (base_path(LangEditController::$langs_folders[$menu_item].$lang_code.'/'.$file_name));
            }
            $context = [];
            $need_language_files = false;
            foreach ($lang_array as$lang_code => $lang)
            {
                if($lang){
                    $this->SetValue($lang,$context[$lang_code]);
                }else{
                    $need_language_files = true;
                }
            }
            return view::make('crud::common.lang_form_edit',compact('context','langs','need_language_files'));
        }
    }

    /**
     * @param $arr    масив з данними
     * @param $context  змінна яка буде писатьсь в файл
     */
    private function GetValue($arr,&$context)
    {
        foreach ($arr as $arr_key => $arr_val) {
            if (!is_array($arr_val)) {
                $context .= '\'' . $arr_key . '\'=>\'' . $arr_val . '\',' . PHP_EOL;
            } else {
                $context .= '\'' . $arr_key . '\'=>[ ' . PHP_EOL;
                $this->GetValue($arr_val, $context);
                $context .= '],' . PHP_EOL;
            }
        }
    }

    /**
     * @param $arr   массив з данними
     * @param $context змінна яка виводиться
     * @param null $parent_val попередній ключ
     */
    private function SetValue($arr,&$context,$parent_val = null){
//        dd($context);
        foreach ($arr as $arr_key=>$arr_val){
            if(!is_array($arr_val)){
                if(is_null($parent_val))
                {
                    $context['['.$arr_key.']'] = $arr_val;
                }else{
                    $context[$parent_val.'['.$arr_key.']'] = $arr_val;
                }
            }else{
                if(is_null($parent_val)){
                    $this->SetValue($arr_val,$context,'['.$arr_key.']');
                }
                else{
                    $this->SetValue($arr_val,$context,$parent_val.'['.$arr_key.']');

                }
            }
        }
    }


    private function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
