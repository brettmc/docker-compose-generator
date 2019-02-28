<?php
//template4.yml merged over the top of template3.yml
return [
    'services' => [
        'three-service' => [
            'image' => 'bar:latest',
            'environment' => [
                'FOO' => '{{FOO}}',
                'BAR' => '{{BAR}}',
                'BAZ' => '{{BAZ}}',
            ],
        ],
    ],
];
