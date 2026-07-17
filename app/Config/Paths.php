<?php

namespace App\Config;

class Paths
{
    public static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function app(): string
    {
        return self::root() . DIRECTORY_SEPARATOR . 'app';
    }

    public static function views(): string
    {
        return self::app() . DIRECTORY_SEPARATOR . 'Views';
    }

    public static function config(): string
    {
        return self::app() . DIRECTORY_SEPARATOR . 'Config';
    }

    public static function public(): string
    {
        return self::root() . DIRECTORY_SEPARATOR . 'public';
    }
}