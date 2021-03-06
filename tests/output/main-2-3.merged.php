<?php
//main*.yml + template2.yml + template3.yml merged
return [
    'version' => '3.4',
    'networks' => [
        'front' => '{{FOO}}-front',
        'back' => '{{FOO}}-back',
    ],
    'services' => [
        'my-service' => [
            'ports' => [
                '80:80',
                '443:443',
            ],
            'labels' => [
                'traefik.docker.network={{FOO}}',
                'traefik.enabled=true',
                'traefik.frontend.rule=HOST my-service.{{BAR}}.example.com',
                'traefik.port=80',
                'traefik.protocol=http',
            ],
            'environment' => [
                'BAR' => '{{BAR}}',
                'BAZ' => '{{BAZ}}',
                'BARBAZ' => '{{BAR}} and {{BAZ}}',
            ],
        ],
        'my-other-service' => [
            'ports' => [
                '80:80',
            ],
            'environment' => [
                'FOO' => '{{FOO}}',
            ],
        ],
        'three-service' => [
            'image' => 'foo:latest',
            'environment' => [
                'FOO' => '{{FOO}}',
            ],
        ],
    ],
];
