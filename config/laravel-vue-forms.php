<?php


return [

    'api_middleware' => ['api'],
    'check_permissions' => env('LVF_CHECK_PERMISSIONS', false),
    'entities_soft_delete' => env('LVF_ENTITIES_SOFT_DELETE', false),
    'values_soft_delete' => env('LVF_VALUES_SOFT_DELETE', false),

    /**
     * Registers open access to the form configuration and form field policies.
     * If set to false, you must specify your own policies for these when using the administration area
     */
    'openAccess' => true,

    /**
     * Allow editing of system level forms
     */
    'edit_system_forms' => env('LVF_EDIT_SYSTEM_FORMS', false),


    /**
     * Admin Configuration
     */
    'use_web_routes' => env('LVF_USE_WEB_ROUTES', true),
    'use_base_api' => env('LVF_USE_BASE_API', true),
    'use_admin_api' => env('LVF_USE_ADMIN_API', true),
    'admin_middleware' => '',


    'entity_type_options' => [
        'model' => 'Built In Entity',
        'custom' => 'Custom Entity'
    ],

    'built_in_entity_types' => [
        'entity_type'        => \jhoopes\LaravelVueForms\Models\EntityType::class,
        'form_configuration' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        'form_field'         => \jhoopes\LaravelVueForms\Models\FormField::class,
    ],

    /**
     * Possible field types to allow for selection when adding a new field
     */
    'widget_types' => [
        'column' => [
            'name' => 'Column',
            'structural' => true,
        ],
        'tab' => [
            'name' => 'Tab',
            'structural' => true,
        ],
        'static' => [
            'name' => 'Static',
        ],
        'text' => [
            'name' => 'Text',
        ],
        'autocomplete' => [
            'name' => 'autocomplete'
        ],
        'textarea' => [
            'name' => 'Generic Textarea',
        ],
        'dropdown' => [
            'name' => 'Select / Dropdown',
        ],
        'multidropdown' => [
            'name' => 'Multi-Select / Dropdown'
        ],
        'checkbox' => [
            'name' => 'Checkbox',
        ],
        'radio' => [
            'name' => 'Radio',
        ],
        'datepicker' => [
            'name' => 'Date Picker',
        ],
        'timepicker' => [
            'name' => 'Time Picker'
        ],
        'datetimepicker' => [
            'name' => 'Date/Time Picker'
        ],
        'files' => [
            'name' => 'File Uploader',
        ],
        'wysiwyg' => [
            'name' => 'Rich text editor',
        ],
        'code' => [
            'name' => 'Code Editor'
        ]
    ],

    /**
     * Possible casting types for casting attributes to
     */
    'cast_types' => [
        'integer' => 'Integer',
        'boolean' => 'Boolean',
        'string' => 'String',
        'array' => 'Array',
        'double' => 'Double / Float'
    ],

    /**
     * Standard validation rules
     * These are rules most found in Laravel's validation rule set
     * The "hasOptions" key allows you to specify additional options for that validation rule.  This is dependent on the rule itself
     */
    'standard_validation_rules' => [
        ['value'=>'accepted', 'name' => 'Accepted'],
        ['value'=>'alpha', 'name' => 'Alphabetic Characters'],
        ['value'=>'alpha_dash', 'name' => 'Alphabetic Characters w/dashes'],
        ['value'=>'alpha_num', 'name' => 'Alpha-numeric Characters'],
        ['value'=>'array', 'name' => 'Array'],
        ['value'=>'between', 'name' => 'Between', 'hasOptions'=>'true', 'options' => ['Min', 'Max']],
        ['value'=>'boolean', 'name' => 'Boolean'],
        ['value'=>'date', 'name' => 'Date'],
        ['value'=>'digits', 'name' => 'Required Number of Digits', 'hasOptions'=>'true', 'options' => ['#']],
        ['value'=>'digits_between', 'name' => 'Between', 'hasOptions'=>'true', 'options' => ['Min', 'Max']],
        ['value'=>'email', 'name' => 'Email'],
        ['value'=>'file', 'name' => 'File'],
        ['value'=>'integer', 'name' => 'Integer'],
        ['value'=>'min', 'name' => 'Min', 'hasOptions'=>'true', 'options' => ['Min']],
        ['value'=>'max', 'name' => 'Max', 'hasOptions'=>'true', 'options' => ['Max']],
        ['value'=>'nullable', 'name' => 'Allowed Empty'],
        ['value'=>'required_if', 'name' => 'Required If', 'hasOptions'=>'true', 'options' => ['Field', 'Value']],
        ['value'=>'required_unless', 'name' => 'Required Unless', 'hasOptions'=>'true', 'options' => ['Field', 'Value']],
        ['value'=>'string', 'name' => 'String'],
    ]
];
