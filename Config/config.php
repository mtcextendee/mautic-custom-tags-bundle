<?php

return [
    'name'        => 'Custom Tags',
    'description' => 'Custom tags for Mautic',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',
    'services'    => [
        'events' => [
            'mautic.plugin.email.custom.tags.subscriber' => [
                'class'     => \MauticPlugin\MauticCustomTagsBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.custom.tags.helper.token',
                ],
            ],
            'mautic.plugin.page.custom.tags.subscriber' => [
                'class'     => \MauticPlugin\MauticCustomTagsBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'mautic.custom.tags.helper.token',
                    'mautic.lead.model.lead',
                    'mautic.security',
                ],
            ],
        ],
        'other' => [
            'mautic.custom.tags.helper.token' => [
                'class'     => \MauticPlugin\MauticCustomTagsBundle\Helper\TokenHelper::class,
                'arguments' => [
                    'mautic.http.client',
                    'mautic.lead.helper.primary_company'
                ],
            ],
        ],
    ],
];
