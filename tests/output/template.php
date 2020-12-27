<?php
//main.yml converted to a PHP array
return [
    'version' => '3.4',
    'networks' => [
        'front' => '{{FOO}}-front',
        'back' => '{{FOO}}-back',
    ],
    'services' => [
        'my-service' => [
            'environment' => [
                'BAR' => '{{BAR}}',
                'BAZ' => '{{BAZ}}',
                'BARBAZ' => '{{BAR}} and {{BAZ}}',
            ],
        ],
    ],
];
