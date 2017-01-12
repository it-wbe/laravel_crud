<?php

namespace Wbe\Crud\Models;

trait Translatable
{
    public static function boot()
    {
        static::addGlobalScope(new \Wbe\Crud\Models\TranslateScope);
    }
}
