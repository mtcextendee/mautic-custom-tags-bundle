<?php

return [
    'name'        => 'Custom Tags',
    'description' => 'Custom tags for emails',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.email.custom.tags.subscriber' => [
                'class'     => \MauticPlugin\MauticCustomTagsBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.http.connector',
                ],
            ],
        ],
    ],
];
