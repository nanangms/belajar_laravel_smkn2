<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('setActive')) {

    function setActive($routePattern, $class = 'active')
    {
        return Request::is($routePattern) ? $class : '';
    }

}

if (!function_exists('openMenu')) {

    function openMenu($routePattern, $class = 'menu-open')
    {
        return Request::is($routePattern) ? $class : '';
    }

}
