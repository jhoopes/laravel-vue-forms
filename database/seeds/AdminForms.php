<?php

use \jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

return [


    'form_configurations' => [
        'form_configuration_form' => [
            'name' => 'form_configuration_form',
            'type' => 'system',
            'active' => 1,
            'entity_name' => 'form_configuration',
            'entity_model' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        ],
        'form_field_form' => [
            'name' => 'form_field_form',
            'type' => 'system',
            'active' => 1,
            'entity_name' => 'form_field',
            'entity_model' => \jhoopes\LaravelVueForms\Models\FormField::class,
        ],
        'entity_type_form' => [
            'name' => 'entity_type_form',
            'type' => 'entity_type_form',
            'active' => 1,
            'entity_name' => 'entity_type',
            'entity_model' => \jhoopes\LaravelVueForms\Models\EntityType::class,
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
                    'alpha_dash'
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
                'default' => false,
                'validation_rules' => [
                    'boolean',
                ]
            ]
        ],
        'fc_entity_type' => [
            'name' => 'fc_entity_type',
            'value_field' => 'entity_type_id',
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
                    'int',
                    'exists:entity_types,id'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/entity_types',
                    'optionLabelField' => 'title',
                    'optionValueField' => 'id',
                ]
            ]
        ],
        'fc_entity_name' => [
            'name' => 'fc_entity_name',
            'value_field' => 'entity_name',
            'label' => 'Entity Name',
            'widget' => 'text',
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
            ]
        ],
        'fc_entity_model' => [
            'name' => 'fc_entity_model',
            'value_field' => 'entity_model',
            'label' => 'Entity Model',
            'widget' => 'text',
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
                    'json'
                ],
                'editorOptions' => [
                    'ace_options' => [],
                    'mode' => 'json',
                    'height' => '200px'
                ]
            ]
        ],
        'ff_col1' => [
            'name' => 'ff_col1',
            'value_field' => null,
            'label' => 'Column1',
            'widget' => 'column',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => null,
            'field_extra' => []
        ],
        'ff_name' => [
            'name' => 'ff_name',
            'value_field' => 'name',
            'label' => 'Form Field Name',
            'widget' => 'text',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'string',
                    'alpha_dash'
                ]
            ]
        ],
        'ff_value_field' => [
            'name' => 'ff_value_field',
            'value_field' => 'value_field',
            'label' => 'Form Field Value Field',
            'widget' => 'text',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'string',
                    'alpha_dash'
                ]
            ]
        ],
        'ff_label' => [
            'name' => 'ff_label',
            'value_field' => 'label',
            'label' => 'Form Field Label',
            'widget' => 'text',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'string',
                ]
            ]
        ],
        'ff_widget' => [
            'name' => 'ff_widget',
            'value_field' => 'widget',
            'label' => 'Field Widget Type',
            'widget' => 'dropdown',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'nullable',
                    'string'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/widget_types',
                    'optionLabelField' => 'title',
                    'optionValueField' => 'value',
                ]
            ]
        ],
        'ff_visible' => [
            'name' => 'ff_visible',
            'value_field' => 'visible',
            'label' => 'Visible',
            'widget' => 'checkbox',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'boolean',
            'field_extra' => [
                'required' => true,
                'default' => true,
                'validation_rules' => [
                    'boolean',
                ]
            ]
        ],
        'ff_disabled' => [
            'name' => 'ff_disabled',
            'value_field' => 'disabled',
            'label' => 'Disabled',
            'widget' => 'checkbox',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'boolean',
            'field_extra' => [
                'required' => true,
                'default' => false,
                'validation_rules' => [
                    'boolean',
                ]
            ]
        ],
        'ff_is_eav' => [
            'name' => 'ff_is_eav',
            'value_field' => 'is_eav',
            'label' => 'Is EAV?',
            'widget' => 'checkbox',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'boolean',
            'field_extra' => [
                'required' => true,
                'default' => false,
                'validation_rules' => [
                    'boolean',
                ]
            ]
        ],
        'ff_cast' => [
            'name' => 'ff_cast',
            'value_field' => 'cast_to',
            'label' => 'Cast To',
            'widget' => 'dropdown',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col1',
            'cast' => 'string',
            'field_extra' => [
                'required' => false,
                'validation_rules' => [
                    'nullable',
                    'string'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/cast_types',
                    'optionLabelField' => 'title',
                    'optionValueField' => 'value',
                ]
            ]
        ],
        'ff_col2' => [
            'name' => 'ff_col2',
            'value_field' => null,
            'label' => 'Column2',
            'widget' => 'column',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => null,
            'field_extra' => []
        ],
        'ff_field_extra' => [
            'name' => 'ff_field_extra',
            'value_field' => 'field_extra',
            'label' => 'Field Options',
            'widget' => 'form-field-extra',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => 'ff_col2',
            'cast' => 'array',
            'field_extra' => [
                'required' => false,
                'validation_rules' => [
                    'nullable',
                    'array',
                    '\\jhoopes\\LaravelVueForms\\App\\ValidationRules\\FieldExtraValidation'
                ],
                'editorOptions' => [
                    'ace_options' => [],
                    'mode' => 'json',
                    'height' => '400px'
                ]
            ]
        ],
        'et_name' => [
            'name' => 'et_name',
            'value_field' => 'name',
            'label' => 'Entity Type Name',
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
                    'alpha_dash'
                ]
            ]
        ],
        'et_title' => [
            'name' => 'et_title',
            'value_field' => 'title',
            'label' => 'Entity Type Title',
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
        'et_type' => [
            'name' => 'et_type',
            'value_field' => 'type',
            'label' => 'Entity Type',
            'widget' => 'dropdown',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'required' => true,
                'validation_rules' => [
                    'nullable',
                    'string'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/entity_type_options',
                    'optionLabelField' => 'title',
                    'optionValueField' => 'name',
                ]
            ]
        ],
        'et_built_in_type' => [
            'name' => 'et_built_in_type',
            'value_field' => 'built_in_type',
            'label' => 'BuiltEntity Type',
            'widget' => 'dropdown',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'validation_rules' => [
                    'requiredIf:type,model',
                    'string'
                ],
                'condition' => [
                    'valueField' => 'type',
                    'fieldValue' => 'model'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/built_in_entity_types',
                    'optionLabelField' => 'title',
                    'optionValueField' => 'name',
                ]
            ]
        ],
        'et_configuration' => [
            'name' => 'et_configuration',
            'value_field' => 'entity_configuration',
            'label' => 'Configuration Options',
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
                    'json'
                ],
                'editorOptions' => [
                    'ace_options' => [],
                    'mode' => 'json',
                    'height' => '200px'
                ]
            ]
        ],
        'et_form_configuration_id' => [
            'name' => 'et_form_configuration_id',
            'value_field' => 'default_form_configuration_id',
            'label' => 'Entity Default Form Configuration',
            'widget' => 'autocomplete',
            'visible' => true,
            'disabled' => false,
            'is_eav' => false,
            'parent_id' => null,
            'cast' => 'string',
            'field_extra' => [
                'validation_rules' => [
                    'nullable',
                    'numeric'
                ],
                'options_config' => [
                    'optionsURL' => LaravelVueForms::adminApiPrefix() . '/form_configurations',
                    'optionLabelField' => 'name',
                    'optionValueField' => 'id',
                ]
            ]
        ],
    ],


    'form_configuration_form_field' => [
        'form_configuration_form' => [
            'fc_name',
            'fc_type',
            'fc_active',
            'fc_entity_type',
            'fc_entity_name',
            'fc_entity_model',
            'fc_options'
        ],
        'form_field_form' => [
            'ff_col1',
            'ff_name',
            'ff_value_field',
            'ff_label',
            'ff_widget',
            'ff_visible',
            'ff_disabled',
            'ff_is_eav',
            'ff_cast',
            'ff_col2',
            'ff_field_extra'
        ],
        'entity_type_form' => [
            'et_name',
            'et_title',
            'et_type',
            'et_built_in_type',
            'et_form_configuration_id',
            'et_configuration',
        ]
    ]


];
