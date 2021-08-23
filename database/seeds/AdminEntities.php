<?php

return [


    'form_configuration' => [
        'name'          => 'form_configuration',
        'title'         => 'Form Configuration',
        'type'          => 'model',
        'built_in_type' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
    ],

    'form_field' => [
        'name' => 'form_field',
        'title' => 'Form Field',
        'type' => 'model',
        'built_in_type' => \jhoopes\LaravelVueForms\Models\FormField::class,
    ],

    'entity_type' => [
        'name' => 'entity_type',
        'title' => 'EntityType',
        'type' => 'model',
        'built_in_type' => \jhoopes\LaravelVueForms\Models\EntityType::class,
    ]

];
