<?php

namespace Wbe\Crud\Models;

trait Translatable
{
    /**
     * @param $lang_id
     * @return \Illuminate\Database\Query\Builder|static
     */
    static public function translate($lang_id)
    {
        $t = new self;
        $table = $t->getTable();
        $desc_table = $table . '_description';

        $cols = \Schema::getColumnListing($table . '_description');
        //foreach ($cols as $k => $c) {
        //    $cols[$k] = $desc_table .  '.' . $c;
        //}
        $sel_raw = [];
        foreach ($cols as $c) {
            if ($c != 'id' && $c != 'lang_id')
                $sel_raw[] = 'COALESCE(' . $desc_table . '.' . $c . ', "[translation not set for \"' . $c . '\"]") as "' . $c . '"';
        }

        //print_r($sel_raw);

        //->addSelect('COALESCE(name, DEFAULT("name"))')   $sel_raw => $cols
        return $t->selectRaw($table . '.*, ' . implode(',', $sel_raw))
            ->leftJoin($table . '_description', [
                [$table . '.id', '=', $table . '_description.content_id'],
                ['lang_id', '=', \DB::raw($lang_id)]
            ])->groupBy($table . '.id');
    }

    public static function boot()
    {
        static::addGlobalScope(new \Wbe\Crud\Models\TranslateScope);
    }
}
