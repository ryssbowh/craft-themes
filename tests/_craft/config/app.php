<?php
use Ryssbowh\CraftThemes\Installer;

return [
    'modules' => [
        'theme-installer' => Installer::class
    ],
    'bootstrap' => ['theme-installer'],
];
