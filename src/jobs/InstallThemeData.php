<?php
namespace Ryssbowh\CraftThemes\jobs;

use Ryssbowh\CraftThemes\Themes;
use craft\queue\BaseJob;

class InstallThemeData extends BaseJob
{
    /**
     * @var string
     */
    public $theme;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            $theme = Themes::$plugin->registry->getTheme($this->theme);
            if (Themes::$plugin->layouts->installThemeData($theme)) {
                $theme->afterThemeInstall();
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        $theme = Themes::$plugin->registry->getTheme($this->theme);
        return \Craft::t('themes', 'Installing {theme} data', ['theme' => $theme->name]);
    }

}