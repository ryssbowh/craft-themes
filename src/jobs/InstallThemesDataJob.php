<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\queue\BaseJob;

/**
 * Install all themes
 */
class InstallThemesDataJob extends BaseJob
{
    public function execute($queue)
    {
        foreach (Themes::$plugin->registry->getNonPartials() as $theme) {
            Themes::$plugin->registry->installTheme($theme);
        }
    }

    public function getDescription()
    {
        return \Craft::t('themes', 'Install all themes data');
    }
}