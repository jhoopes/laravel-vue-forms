<?php

namespace jhoopes\LaravelVueForms;

class LaravelVueForms
{

    public static $models = [
        'form_configuration' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        'form_field'         => \jhoopes\LaravelVueForms\Models\FormField::class,
    ];


    public static function model($model, $concrete = null)
    {
        if ($concrete !== null) {
            self::$models[$model] = $concrete;
        }

        return app(self::$models[$model]);
    }


    protected static $apiPrefix = '/api/forms';
    protected static $adminApiPrefix = '/api/forms/admin';
    protected static $webAdminPrefix = '/admin';
    protected static $adminAuthorization = '';

    public static function apiPrefix($apiPrefix = null)
    {
        if ($apiPrefix !== null) {
            self::$apiPrefix = $apiPrefix;
        }

        return self::$apiPrefix;
    }

    public static function adminApiPrefix($apiPrefix = null)
    {
        if ($apiPrefix !== null) {
            self::$adminApiPrefix = $apiPrefix;
        }

        return self::$adminApiPrefix;
    }

    public static function webAdminPrefix($webPrefix = null)
    {
        if ($webPrefix !== null) {
            self::$webAdminPrefix = $webPrefix;
        }

        return self::$webAdminPrefix;
    }


    public static function adminAuthorization($authorization = null)
    {
        if ($authorization) {
            self::$adminAuthorization = $authorization;
        }

        return self::$adminAuthorization;
    }

}
