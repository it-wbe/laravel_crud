<?php

namespace Wbe\Crud\Models;

use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\Globals;
use Illuminate\Database\Eloquent\Model;

class ModelGenerator
{
    /** @var string Строка для визначення, чи перегенеровувати всю модель */
    const regenerate_entire_model_ident = '[leave this text to regenerate entire model]';

    /*public function get_model_relation_method($left_content, $right_content)
    {
        $fn = ContentType::getFilePathByModel($right_content->model);
        return $this->getModelRelationsMethod($left_content->table, file_get_contents($fn));
    }*/

    //static public function get_content_model_relation($k, $left_content_type_id, &$left_relation /*//[right], &$right_relation*/)

    /**
     * Генерація методу відношення на основі параметрів із POST запиту форми FieldsDescriptor
     * @param $left_content object контент зліва
     * @param $right_content object контент справа
     * @param $post array масив POST параметрів запиту форми FieldsDescriptor
     * @param $left_relation string
     * @return bool
     */
    static public function get_content_model_relation($left_content, $right_content, $post, &$left_relation)
    {
        //$rel_type, $right_name, $left_column, $right_column, $req_table_to,  $left_content_type_id

        //$left_content = ContentType::translate(session('admin_lang_id'))->find($left_content_type_id);

        //$right_content_id = \Request::input('rel_right_content_type')[$k];
        //$right_content = ContentType::translate(session('admin_lang_id'))->find($right_content_id);

        if (!$right_content) {
            $left_relation = false;
            return false;
        }

        //$left_name = $left_content->table;
        //$right_name = trim(\Request::input('rel_method_name')[$k]); //$right_content->table;

        //$left_model = 'App\Models\\' . $left_content->model;
        $right_model = $right_content->model;

        //$left_model_table = $left_content->table;
        //$right_model_table = $right_content->table;
        //$req_table_to = trim(\Request::input('rel_table_to')[$k]);
        $l_to_r_model_table = $post['rel_table_to']
            ? $post['rel_table_to']
            : $left_content->table . '_to_' . $right_content->table;

        //$left_column = \Request::input('rel_left_column')[$k];
        //$right_column = \Request::input('rel_right_column')[$k];

        //XEDIT
        /*if ($rp['rel_type']) {
            $tmp = $rp['left_column'];
            $left_column = $rp['right_column'];
            $right_column = $tmp;
        }*/


        switch ($post['rel_type']) {
            case 'hasOne':
                $left_relation =
                    self::generate_relation_method(
                        $post['right_name'],
                        $post['rel_type'],
                        $right_model,
                        [$post['right_column'], $post['left_column']]
                    );
                break;
            case 'hasMany':
                $left_relation =
                    self::generate_relation_method(
                        $post['right_name'],
                        $post['rel_type'],
                        $right_model,
                        [$post['right_column'], $post['left_column']]
                    );
                break;
            case 'belongsToMany':
                $left_relation =
                    self::generate_relation_method(
                        $post['right_name'],
                        $post['rel_type'],
                        $right_model,
                        [$l_to_r_model_table, $post['left_column'], $post['right_column']]
                    );
                break;
            case 'belongsTo':
                $left_relation =
                    self::generate_relation_method(
                        $post['right_name'],
                        $post['rel_type'],
                        $right_model,
                        [$post['right_column'], $post['left_column']]
                    );
                break;
            default:
                $left_relation = '/* undefined relation: ' . $post['rel_type'] . ' */';

        }
        return true;
    }

    /**
     * Генерація строки методу відношення
     * @param $method_name
     * @param $rel_type
     * @param $rel_model_name
     * @param $params array
     * @return string
     */
    static public function generate_relation_method($method_name, $rel_type, $rel_model_name, $params = [])
    {
        foreach ($params as $k => $v)
            $params[$k] = '\'' . $v . '\'';
        return "
    public function $method_name()
    {
        return \$this->$rel_type('$rel_model_name'" . ($params ? ', ' . implode(', ', $params) : '') . ");
    }
";
    }

    /**
     * Записати масив зв'язків $new_relations у модель $content_type
     * @param $content_type ContentType тип контенту
     * @param $new_relations array масив зв'язків
     * @return bool
     */
    static public function write_content_model(ContentType $content_type, $new_relations = [])
    {
        $table = $content_type->table;
        $desc_table = $table . '_description';

        if (\Schema::hasTable($table)) {

            // $content_description_model_template
            ///$cdm_template = \File::get(storage_path('generator/ContentModel.txt'));
            $cdm_template = \File::get(__DIR__ . '/generator/ContentModel.txt');


            $classname = $content_type::getCTModel($content_type->model);
            if (!$classname)
                die('model not found: ' . $content_type->model);

            //$filename = ContentType::getFilePathByModel($classname);
            $filename = $content_type->getClassFilename($classname); //!!!

            //echo ContentType::getFilePathByModel($classname);
            //echo '---';
            //echo $content_type->getClassFilename($classname);

            //base_path() . '\app\Models\\' . ltrim($classname, '\\/') . '.php';

            //(!class_exists('App\Models\\' . $classname)) ||
            if ((!file_exists($filename)) ||
                (strpos(file_get_contents($filename), self::regenerate_entire_model_ident) !== false)
            ) {

                $fields = \Schema::getColumnListing($table);
                $no_timestamps = !(
                    in_array('created_at', $fields) &&
                    in_array('updated_at', $fields) &&
                    in_array('deleted_at', $fields)
                );
                $translate = (\Schema::hasTable($desc_table));

                $content = ''; //$this->rel_model_delimiter_begin . PHP_EOL . $this->rel_model_delimiter_end;

                $classname_namespace = before_last('\\', $classname, 1);
                $cdm_template = bind_string($cdm_template, [
                    'namespace' => $classname_namespace
                        ? /*'App\Models\ContentTypes\\' .*/ $classname_namespace
                        : 'App\Models\ContentTypes',
                    'classname' => after_last('\\', $classname),
                    'table' => $table,
                    'translate' => $translate,
                    'no_timestamps' => $no_timestamps,
                    'content' => $content,
                ]);

                //$filename = self::getClassFilename('App\Models\\' . $classname);

                //file_put_contents($filename, $cdm_template);
                Globals::$messages[0][] = 'writing model "' . $classname . '" to "' . $filename . '"';
            } else {

                Globals::$messages[0][] = 'file "' . $filename . '" already exists, removing and writing relations...';
                $cdm_template = file_get_contents($filename);
            }

            foreach ($new_relations as $new_relation_k => $new_relation) {
                $cdm_template = self::createOrUpdateMethod($new_relation_k, $new_relation, $cdm_template);
            }

            file_put_contents($filename, $cdm_template);
        } else Globals::$messages[3][] = 'table "' . $content_type->table . '" not found! cannot write model';
        return true;
    }


    /**
     * Генерація опису поля на основі даних зі схеми таблиці БД
     * @param $field \stdClass \DB::select('SHOW COLUMNS FROM ' . $table);
     * @param $content_type_id int ID типу контенту
     * @param $default_field ContentTypeFields шаблон поля, знаходиться у БД
     * @return \Illuminate\Database\Eloquent\Model
     */
    static public function autofield($field, $content_type_id, ContentTypeFields $default_field)
    {
        $new_field = $default_field->replicate();
        $new_field->name = $field->Field;
        $new_field->sort = -9999;
        $new_field->content_type_id = $content_type_id;

        //$new_field->type = $field->Type;

        //created_at

        $new_field->validators = '';
        if ($field->Field == 'id' || $field->Field == 'content_id' || $field->Field == 'lang_id') {
            $new_field->type = 'text';
            $new_field->validators .= 'integer|';
        } elseif ((starts_with($field->Type, 'tinyint(') || (starts_with($field->Type, 'int(')))
            && ends_with($field->Field, '_id')
            && (\Schema::hasTable(before('_id', $field->Field)))
        ) {
            $new_field->type = 'rel:select';
            $new_field->validators .= 'integer|';
            //$new_field->form_attributes = '$f->options(\Illuminate\Support\Facades\DB::table("' . before('_id', $field->Field) . '")->pluck("name","id"));';
        } elseif ($field->Type == 'tinyint(1)') {
            $new_field->type = 'checkbox';
            $new_field->validators .= 'integer|';
        } elseif (starts_with($field->Type, 'int(') || starts_with($field->Type, 'tinyint(')) {
            $new_field->type = 'text';
            $new_field->validators .= 'integer|';
        } elseif (starts_with($field->Type, 'varchar(') || starts_with($field->Type, 'char(')) {
            $new_field->type = 'text';
            $maxlen = between('(', ')', $field->Type);
            if ($maxlen)
                $new_field->validators .= 'max:' . $maxlen . '|';
        } elseif (starts_with($field->Type, 'text')) {
            $new_field->type = 'Wbe\Crud\Models\Rapyd\Fields\Ckeditor'; //radactor
            $new_field->validators .= 'max:4096|';
        } elseif ($field->Type == 'datetime') {
            $new_field->type = 'date';
            $new_field->validators .= 'date|';
        }

        if (($field->Null == 'NO') && (!in_array($field->Field, ['created_at', 'updated_at', 'deleted_at']))) {
            $new_field->validators .= 'required|';
        }

        $new_field->validators = rtrim($new_field->validators, '|');

        return $new_field;
    }


    /**
     * Створення чи оновлення методу в моделі
     * @param $method_name string назва методу
     * @param $method string тіло методу
     * @param $filestring string тіло моделі
     * @return string
     */
    static public function createOrUpdateMethod($method_name, $method, $filestring)
    {
        preg_match_all('~(?<=public\sfunction\s)' . $method_name . '\(~', $filestring, $method_match);
        //print_r($method_match);
        //echo '[[[' . $method_name . '; ' . $method . '; ' . $filestring . ']]]';
        if (isset($method_match[0][0])) {
            return preg_replace(
                '~\s*public\sfunction\s' . $method_name . '\(.+?\}\s*~s',
                PHP_EOL . trim($method, ' ') . PHP_EOL,
                $filestring
            );
        } else {
            return preg_replace('~\}\s*\Z~', $method . '}', $filestring);
        }
    }

    /*public function getModelRelationsMethod($method_name, $filestring)
    {
        $method_body = trim(trim(between('public function ' . $method_name . '()', '}', $filestring)), '{} ');
        preg_match_all('~(?<=\$this\-\>).*(?=\()|(?<=\().*(?=\);)~', $method_body, $method_params);
        return $method_params[0];
    }*/

    /**
     * Отримати масив всіх зв'язків у моделі
     * @param $filestring string тіло моделі
     * @param $search_for_method string фільтр за назвою методу
     * @return array|mixed|string
     */
    static public function getModelRelationsMethods($filestring, $search_for_method = '')
    {
        preg_match_all('~public\sfunction\s' . ($search_for_method ? $search_for_method . '\(' : '') . '.*\}~sU',
            $filestring,
            $methods_str
        );
        //echo '~public\sfunction\s' . $search_for_method . '.*\}~sU';
        $methods = [];
        foreach ($methods_str[0] as $method) {
            preg_match_all('~(?<=public\sfunction\s).*(?=\()|(?<=\$this\-\>).*(?=\()|(?<=\().*(?=\);)~',
                $method,
                $method_params
            );
            $methods[$method_params[0][0]] = $method_params[0];
        }

        foreach ($methods as $rel_m_k => $rel_m) {
            if (isset($methods[$rel_m_k][2])) {

                //print_r($relation_methods[$rel_m_k]);
                $methods[$rel_m_k][2] = explode(',', $methods[$rel_m_k][2]);
                foreach ($methods[$rel_m_k][2] as $k => $v) {
                    $methods[$rel_m_k][2][$k] = trim($v, ' "\'()');
                }
            } //else echo '!isset($relation_methods[$rel_m_k][2])';
        }

        if ($search_for_method)
            return isset($methods[$search_for_method]) ? $methods[$search_for_method] : false; //'method not found'
        else
            return $methods;
    }

    /**
     * Отримати тип контенту за моделлю. Використовується для отримання типу контенту зі зв'язків
     * @param $model string
     * @return ContentType
     */
    static public function getContentTypeByModel($model)
    {
        if (strpos($model, '::class') === false) {
            return ContentType::where('model', $model)->first();
        } else {
            $model = trim(str_replace('::class', '', $model));
            $sql = '((content_type.model = "' . $model . '") OR (content_type.model LIKE "%\\' . $model . '"))';
            //echo $sql;
            return ContentType::whereRaw($sql)->first();
        }
    }

    /**
     * Отримати *приблизну* назву файлу на назвою класу
     * @param $classname string назва класу
     * @return string
     */
    static public function getModelFilename($classname)
    {
        //return '../app/Models/' . ltrim($classname, '\\/') . '.php';
        \File::makeDirectory(base_path() . '/app/Models/ContentTypes/', 0777, true, true);
        //return base_path() . '/app/Models/ContentTypes/' . str_replace('\\', '/', ltrim($classname, '\\/')) . '.php';

        return base_path() . '/'
        . str_replace('\\', '/', str_replace('App\\', 'app\\', ltrim($classname, '\\/'))) . '.php';
    }

    /**
     * Згенерувати файл моделі на основі таблиці $table, якщо вона існує
     * @param $table string таблиця, на основі якої генерувати
     * @param $desc_table string табилця з перекладом (якщо існує)
     * @param $classname string назва класу (якщо потрібно інший)
     * @return bool
     */
    static public function generateModelByTable($table, $desc_table = '', $classname = '')
    {
        //$table = $content->table;
        //$desc_table = $table . '_description';

        if (\Schema::hasTable($table)) {

            //$classname = $content->model;

            $filename = self::getModelFilename($classname);
            //!!! echo $filename.'-';echo ContentType::getCTModel($classname);echo '123';
            //(!class_exists('App\Models\\' . $classname)) ||
            if ((!ContentType::getCTModel($classname)) ||
                (
                    file_exists($filename) &&
                    (strpos(file_get_contents($filename), '[leave this text to regenerate]') !== false)
                )
            ) {

                // $content_description_model_template
                ///$cdm_template = \File::get(storage_path('generator/ContentModel.txt'));
                $cdm_template = \File::get(__DIR__ . '/generator/ContentModel.txt');

                $fields = \Schema::getColumnListing($table);
                $no_timestamps = !(
                    in_array('created_at', $fields) &&
                    in_array('updated_at', $fields) &&
                    in_array('deleted_at', $fields)
                );
                $translate = (\Schema::hasTable($desc_table));

                $model_content = '';

                $classname_namespace = before_last('\\', $classname, 1);
                $cdm_template = bind_string($cdm_template, [
                    'namespace' => $classname_namespace ? /*'App\Models\\' .*/
                        $classname_namespace : 'App\Models\ContentTypes',
                    'classname' => after_last('\\', $classname),
                    'table' => $table,
                    'translate' => $translate,
                    'no_timestamps' => $no_timestamps,
                    'content' => $model_content,
                ]);

                //$filename = self::getClassFilename('App\Models\\' . $classname);

                if ((!file_exists($filename)) ||
                    (strpos(file_get_contents($filename), '[leave this text to regenerate]') !== false)
                ) {
                    echo '<b>writing model "' . $classname . '" to "' . $filename . '"</b><br>';

                    file_put_contents($filename, $cdm_template);
                } else {
                    echo '<b>file "' . $filename . '" already exists! cannot write model "' . $classname . '"</b><br>';
                }

                return 1;
            }
        } else {
            die('no table ' . $table);
        }
        return true;
    }
}