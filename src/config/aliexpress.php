<?php

return [
    'models' => [
        'import' => [
            'presenter' => \Corals\Modules\Aliexpress\Transformers\ImportPresenter::class,
            'resource_url' => 'aliexpress/imports',
        ],
    ],
    'settings' => [
        'api' => [
            'appKey' => [
                'type' => 'text',
                'label' => 'Aliexpress::labels.settings.appKey',
                'required' => true,
                'validation' => '',
                'value' => null,
                'attributes' => [
                    'help_text' => '',
                ],
            ],
            'secretKey' => [
                'type' => 'text',
                'label' => 'Aliexpress::labels.settings.secretKey',
                'required' => true,
                'validation' => null,
                'value' => null,
                'attributes' => [
                    'help_text' => '',
                ],
            ],
            'country' => [
                'type' => 'text',
                'label' => 'Aliexpress::labels.settings.country',
                'required' => false,
                'validation' => null,
                'value' => null,
                'attributes' => [
                    'help_text' => 'Aliexpress::labels.settings.country_help',
                ],
            ],
            'currency' => [
                'type' => 'text',
                'label' => 'Aliexpress::labels.settings.currency',
                'required' => false,
                'validation' => null,
                'value' => null,
                'attributes' => [
                    'help_text' => 'Aliexpress::labels.settings.currency_help',
                ],
            ],
            'language' => [
                'type' => 'text',
                'label' => 'Aliexpress::labels.settings.language',
                'required' => false,
                'validation' => null,
                'value' => null,
                'attributes' => [
                    'help_text' => 'Aliexpress::labels.settings.language_help',
                ],
            ],
        ],
    ],

];
