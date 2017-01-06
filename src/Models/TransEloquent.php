<?

namespace Wbe\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class TransEloquent extends \Eloquent {

    //protected $table = parent::$table;

    //use Translatable;
    // Override the parent method
    /*public function newCollection(array $models = Array())
    {
        return new Extensions\CustomCollection($models);
    }*/
    /*static public function create() {
        //$cl = /Model;
        $new_instance = new parent();
        $new_instance = $new_instance::translate(sesstion('admin_lang_id'));
        return $new_instance;
    }*/

    public static function boot()
    {   //dd(parent);
        parent::boot();

        static::created(function($model)
        {
            // Do something with the model after it's created
            $model = $model->setTable('outcomes');
            return $model::translate(session('admin_lang_id'));
        });
    }
}