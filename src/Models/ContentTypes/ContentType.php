<?php

namespace Wbe\Crud\Models\ContentTypes;

use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\Translatable;

class ContentType extends \Eloquent
{
    use Translatable;

    public $timestamps = false;
    protected $table = 'content_type';


    /**
     * Отримати шлях до файлу моделі
     * @param $model
     * @return string
     */
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

    /**
     * Отримати шлях до файлу класу за його назвою
     * @param $classname
     * @return string
     */
    static public function getClassFilename($classname)
    {
        //ToDo: newly created class cannot autoload (writing model "..."-> Reflection: Class does not exist)
        $reflector = new \ReflectionClass($classname);
        return $reflector->getFileName();
    }

    /*public function getCTModelFilename()
    {
    }*/

    /**
     * Автодоповнення назви моделі, якщо повний неймспейс не вказано.
     * Використовується для створення моделі на основі запису в content_type.model
     * @param $model_name
     * @return bool|string
     */
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