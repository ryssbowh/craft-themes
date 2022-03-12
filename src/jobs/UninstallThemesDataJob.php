<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\queue\BaseJob;

/**
 * Uninstall all themes
 *
 * @since 3.1.0
 */
class UninstallThemesDataJob extends BaseJob
{
    public function execute($queue)
    {
        foreach (Themes::$plugin->registry->getNonPartials() as $theme) {
            Themes::$plugin->registry->uninstallTheme($theme);
        }
    }

    public function getDescription()
    {
        return \Craft::t('themes', 'Uninstall all themes data');
    }
}