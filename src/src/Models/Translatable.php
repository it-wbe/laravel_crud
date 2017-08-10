<?php

namespace Wbe\Crud\Models;

trait Translatable
{
    /**
     * Автозагрузка перекладів для кожної створеної моделі
     */
    public static function boot()
    {
        static::addGlobalScope(new \Wbe\Crud\Models\TranslateScope);
    }
}
