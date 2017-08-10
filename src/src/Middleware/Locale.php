<?php

namespace Wbe\Crud\Middleware;

use Closure;
use App;
use Config;
use Session;
use Request;

class Locale
{
    /**
     * Встановлення локалі на основі session('locale') чи session('admin_locale')
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (starts_with(Request::path() . '/', 'admin')) {
            $raw_locale = Session::get('admin_locale');
        } else $raw_locale = Session::get('locale');
        $locale = $raw_locale;
        /* if (in_array($raw_locale, Config::get('app.locales'))) {
             $locale = $raw_locale;
         } else {
             $locale = Config::get('app.locale');
         }*/
        //echo $locale; exit;
        App::setLocale($locale);

        return $next($request);
    }
}
