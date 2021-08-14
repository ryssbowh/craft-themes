<?php

namespace Ryssbowh\CraftThemesTests\helpers;

use Codeception\Module;
use Composer\Factory;
use Composer\IO\BufferIO;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\Locker;

class ComposerHelper
{
    public static function installBootstrapTheme()
    {
        $io = new BufferIO;
        $jsonPath = realpath(__DIR__ . '/../../composer.json');
        $lockPath = realpath(__DIR__ . '/../../composer.lock');
        $config = json_decode(file_get_contents($jsonPath), true);
        $config['require']["ryssbowh/child-theme"] = '*';
        $config['repositories']['0'] = [
            "type" => "path",
            "url" => "tests/themes/*",
            "options" => [
                "symlink" => true
            ]
        ];
        $composer = Factory::create($io, $config);
        $im = $composer->getInstallationManager();
        $locker = new Locker($io, new JsonFile($lockPath, null, $io), $im, file_get_contents($jsonPath));
        $composer->setLocker($locker);
        $installer = Installer::create($io, $composer);
        $installer->setIgnorePlatformRequirements(true)
            ->setDevMode(true)
            ->setOptimizeAutoloader(true)
            ->setWriteLock(false);
        $installer->setUpdate(true)->setUpdateAllowList(['ryssbowh/child-theme' => '*']);
        $installer->run();
        codecept_debug($io->getOutput());
    }
}