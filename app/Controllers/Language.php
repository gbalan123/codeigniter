<?php

namespace App\Controllers;

class Language extends BaseController
{
    /**
     * Set language in cookie for change language
     */
    public function set_lang_cookie($language)
    {
        helper('cookie');
        $config = config('App');
        $params = array(
            'expires'   => time() + (86400),
            'path'      => $config->cookiePath,
            'domain'    => $config->cookieDomain,
            'secure'    => $config->cookieSecure,
            'httponly'  => $config->cookieHTTPOnly,
            'samesite'  => $config->cookieSameSite,
        );
        setcookie('lang',$language,$params);
        return redirect()->back();
    }
}