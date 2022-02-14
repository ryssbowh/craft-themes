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
    public static function installChildTheme()
    {
        codecept_debug('Requiring themes in composer');
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
        $rm = $composer->getRepositoryManager();
        $lock = new JsonFile($lockPath, null, $io);
        $version = static::findComposerVersion($lock->read());
        if (version_compare($version, '1.10.10', '<=')) {
            $locker = new Locker($io, $lock, $rm, $im, file_get_contents($jsonPath));
        } else {
            $locker = new Locker($io, $lock, $im, file_get_contents($jsonPath));
        }
        $composer->setLocker($locker);
        $installer = Installer::create($io, $composer);
        $installer->setDevMode(true)
            ->setOptimizeAutoloader(true)
            ->setWriteLock(false);
        $installer->setUpdate(true)->setUpdateAllowList(['ryssbowh/child-theme' => '*']);
        $installer->run();
        codecept_debug($io->getOutput());
    }

    protected static function findComposerVersion(array $json)
    {
        foreach ($json['packages'] as $data) {
            if ($data['name'] == 'composer/composer') {
                return $data['version'];
            }
        }
    }
}