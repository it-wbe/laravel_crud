<?php

namespace Wbe\Crud\Models\ContentTypes;

use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\Translatable;

class ContentType extends \Eloquent
{
    use Translatable;

    public $timestamps = false;
    protected $table = 'crud_content_type';

    /*public $messages = [0 => [], 1 => [], 2 => [], 3 => []];
    public $message_class = [0 => 'info', 1 => 'success', 2 => 'warning', 3 => 'danger'];*/


    static public function getFilePathByModel($model)
    {
        $divider = 'Models\\';
        $part1 = before($divider, $model);
        $part2 = after($divider, $model);
        $part1 = strtolower($part1);

        $model = $part1 . $divider . $part2;

        $model = str_replace('\\', '/', ltrim($model, '\\/'));

        $model = str_replace('wbe/crud', 'wbe/crud/src', $model);

        if (starts_with($model, 'app/'))
            $dir = '/';
        else
            $dir = '/packages/';

        // '/app/Models/' .
        $fn = base_path() . $dir . $model . '.php';

        return $fn;
    }

    static public function getClassFilename($classname)
    {
        $reflector = new \ReflectionClass($classname);
        return $reflector->getFileName();
    }

    /*public function getCTModelFilename()
    {
    }*/

    static public function getCTModel($model_name)
    {
        if (class_exists($model_name))
            return $model_name;

        $model = 'App\Models\ContentTypes\\' . $model_name;
        if (!class_exists($model))
            $model = 'Wbe\Crud\Models\ContentTypes\\' . $model_name;

        if (!class_exists($model))
            return false;

        return $model;
    }

}