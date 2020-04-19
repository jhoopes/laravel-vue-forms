<?php

use jhoopes\LaravelVueForms\Facades\LaravelVueForms;

return [


    'form_configurations' => [
        'form_configuration_form' => [
            'name' => 'form_configuration_form',
            'type' => 'system',
            'active' => 1,
            'entity_name' => 'form_configuration',
            'entity_model' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        ],
        'form_field_text_form' => [
            'name' => 'form_field_text_form',
            'type' => 'system',
            'active' => 1,
            'entity_name' => 'form_field',
            'entity_model' => \jhoopes\LaravelVueForms\Models\FormField::class,
        ]
    ],


    'form_fields' => [
        'fc_name' => [
            'name' => 'fc_name',
            'value_field' => 'name',
            'label' => 'Form Configuration Name',
            'widget' => 'text',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'string',
                ]
            ]
        ],
        'fc_type' => [
            'name' => 'fc_type',
            'value_field' => 'type',
            'label' => 'Form Configuration Type',
            'widget' => 'text',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'required' => false,
                'helpText' => 'Optional field to denote a type of form configuration.',
                'validation_rules' => [
                    'string',
                    'alpha_num'
                ],
            ]
        ],
        'fc_active' => [
            'name' => 'fc_active',
            'value_field' => 'active',
            'label' => 'Active',
            'widget' => 'checkbox',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'boolean',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'boolean',
                ]
            ]
        ],
        'fc_entity_name' => [
            'name' => 'fc_entity_name',
            'value_field' => 'entity_name',
            'label' => 'Entity Type',
            'widget' => 'dropdown',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'required' => false,
                'validation_rules' => [
                    'nullable',
                    'string'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/entity_types',
                    'optionLabelField' => 'name',
                    'optionValueField' => 'name',
                ]
            ]
        ],
        'fc_options' => [
            'name' => 'fc_options',
            'value_field' => 'options',
            'label' => 'Options',
            'widget' => 'code',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'array',
            'field_extra' => [
                'required' => false,
                'validation_rules' => [
                    'nullable',
                    'array',
                ]
            ]
        ],
    ],


    'form_configuration_form_field' => [
        'form_configuration_form' => [
            'fc_name',
            'fc_type',
            'fc_active',
            'fc_entity_name',
            'fc_options'
        ]
    ]


];
