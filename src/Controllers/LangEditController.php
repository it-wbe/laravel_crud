<?php

namespace Wbe\Crud\Controllers;

use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
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
    /**
     * Редагування мовних файлів
     * @param Request $r
     * @return View
     */
    public function edit(Request $r)
    {
//        dd($r);
        if($r->isMethod('post')){
            /// save value to file
            //$file_array = include (base_path().'/'.$r->get('path'));
            $context ='';
            $context .='<?php '.PHP_EOL;
            $context .='return ['.PHP_EOL;
            $context_val = \Request::all();
            unset($context_val['_token']);
            unset($context_val['path']);
            $this->GetValue($context_val,$context);
            $context .='];';
            $put_error  = file_put_contents(base_path().'/'.$r->get('path'),$context);
            if($put_error){
                $r->session()->flash('alert-success', 'Language file successful saved!');
            }else{
                $r->session()->flash('alert-danger', 'Something go wrong!');
            }
            return redirect()->back();

        }else{///// GET method

            $lang_array = include (base_path().'/'.$r->get('path'));
            $context = [];
            $this->SetValue($lang_array,$context);
            return view::make('crud::common.lang_form_edit',compact('context'));
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
        foreach ($arr as $arr_key=>$arr_val){
            if(!is_array($arr_val)){
                if(is_null($parent_val))
                {
                    $context[$arr_key] = $arr_val;
                }else{
                    $context[$parent_val.'['.$arr_key.']'] = $arr_val;
                }
            }else{
                if(is_null($parent_val)){
                    $this->SetValue($arr_val,$context,$arr_key);
                }
                else{
                    $this->SetValue($arr_val,$context,$parent_val.'['.$arr_key.']');

                }
            }
        }
    }
}
