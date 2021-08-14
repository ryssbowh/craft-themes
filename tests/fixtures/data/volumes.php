<?php 

use craft\volumes\Local;

return [
    [
        'type' => Local::class,
        'handle' => 'myVolume',
        'name' => 'My Volume',
        'settings' => [
            'path' => '@volumes/myVolume'
        ]
    ],
    [
        'type' => Local::class,
        'handle' => 'myOtherVolume',
        'name' => 'My Other Volume',
        'settings' => [
            'path' => '@volumes/myOtherVolume'
        ]
    ]
];