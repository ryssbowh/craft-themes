<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\queue\BaseJob;

/**
 * Reinstall all layouts
 *
 * @since 3.1.0
 */
class ReinstallLayoutsJob extends BaseJob
{
    public function execute($queue)
    {
        Themes::$plugin->layouts->installAll();
    }

    public function getDescription()
    {
        return \Craft::t('themes', 'Reinstall themes layouts');
    }
}