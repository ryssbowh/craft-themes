<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\helpers\Queue;
use craft\queue\BaseJob;

class InstallThemesData extends BaseJob
{
    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        \Craft::info('installthemedata ' . Themes::$plugin->is(Themes::EDITION_PRO));
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            foreach (Themes::$plugin->registry->getNonPartials() as $theme) {
                Queue::push(new InstallThemeData([
                    'theme' => $theme->handle
                ]));
            }
        }
    }
}