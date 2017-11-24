<?php

namespace Wbe\Crud\Middleware;

use Closure;
use \Illuminate\Support\Facades\App;
use Session;
use Config;
use Redirect;

class LangMiddleware
{
    /**
     * Встановлення локалі на основі домену
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		preg_match('~setlocale~', url()->current(), $setlocale);
		if(empty($setlocale)) {
			//$url_array = explode('--', parse_url($request->url(), PHP_URL_HOST));
			$url_array = explode('.', parse_url($request->url(), PHP_URL_HOST));
			$subdomain = $url_array[0];
			if (in_array($subdomain, \Wbe\Crud\Models\ContentTypes\Languages::pluck('code')->toArray())) {
				Session::put('locale', $subdomain);
                Session::put('lang_id', \Wbe\Crud\Models\ContentTypes\Languages::where('code', $subdomain)->value('id'));
                \App::setLocale($subdomain);
            } else {
				Session::put('lang_id', \Wbe\Crud\Models\ContentTypes\Languages::where('code', Config::get('app.fallback_locale'))->value('id'));
                Session::put('locale', Config::get('app.fallback_locale'));
                \App::setLocale(config('app.fallback_locale'));
            }
			return $next($request);
		}
		return $next($request);
    }
}
