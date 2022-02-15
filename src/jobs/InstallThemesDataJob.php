<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\queue\BaseJob;

class InstallThemesDataJob extends BaseJob
{
    public $uninstall;

    public function execute($queue)
    {
        if ($this->uninstall) {
            Themes::$plugin->registry->uninstallAll();
        } else {
            Themes::$plugin->registry->installAll(true);
        }
    }

    public function getDescription()
    {
        return \Craft::t('themes', ($this->uninstall ? 'Uninstalling ' : 'Installing ') . 'themes data');
    }
}