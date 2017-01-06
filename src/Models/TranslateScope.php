<?php

namespace Wbe\Crud\Models;

//use Wbe\Crud\Models\Translatable;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TranslateScope implements Scope
{
    //use Translatable;
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //$t = new self;
        $table = $model->getTable();
        $desc_table = $table . '_description';

        $cols = \Schema::getColumnListing($table . '_description');
        /*foreach ($cols as $k => $c) {
            $cols[$k] = $desc_table .  '.' . $c;
        }*/
        $sel_raw = [];
        foreach ($cols as $c) {
            if ($c != 'id' && $c != 'lang_id')
                $sel_raw[] = 'COALESCE(' . $desc_table . '.' . $c . ', "[translation not set for \"' . $c . '\"]") as "' . $c . '"';
        }

        //print_r($sel_raw);
        //echo is_admin_panel();

        //->addSelect('COALESCE(name, DEFAULT("name"))')   $sel_raw => $cols
        $builder->selectRaw($table . '.*, ' . implode(',', $sel_raw))
            ->leftJoin($table . '_description', [
                [$table . '.id', '=', $table . '_description.content_id'],
                ['lang_id', '=', \DB::raw(get_current_lang()) ]
            ])
            ->groupBy($table . '.id');
        //['lang_id', '=', \DB::raw(session('admin_lang_id'))]
    }
}