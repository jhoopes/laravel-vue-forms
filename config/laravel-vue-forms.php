<?php


return [

    'api_middleware' => '',
    'check_permissions' => true,
    'values_soft_delete' => false,

    'openAccess' => true,


    /**
     * Admin Configuration
     */
    'use_web_routes' => true,
    'use_admin_api' => true,
    'admin_middleware' => '',



    'entity_types' => [
        'form_configuration' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        'form_field' => \jhoopes\LaravelVueForms\Models\FormField::class,
    ]
];
