<?php
namespace Ryssbowh\CraftThemes\behaviors;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Volume;
use craft\elements\GlobalSet;
use craft\models\CategoryGroup;
use craft\models\EntryType;
use craft\models\TagGroup;
use yii\base\Behavior;

/**
 * This behaviour is attached to some Craft elements (CategoryGroup, EntryType, Volume, TagGroup, GlobalSet)
 * 
 * It gives a shorter way to access those element's layouts, and edit displays/blocks urls.
 * $group = \Craft::$app->categories->getGroupById(1);
 * $layout = $group->getLayout('theme-handle');
 */
class LayoutBehavior extends Behavior
{
    /**
     * @var CategoryGroup|EntryType|Volume|TagGroup|GlobalSet
     */
    public $owner;

    /**
     * @var string
     */
    public $type;

    /**
     * Layout getter
     * 
     * @param  string|ThemeInterface|null $theme theme instance or theme handle, or null for the current theme
     * @return ?LayoutInterface
     */
    public function getLayout($theme = null): ?LayoutInterface
    {
        if (is_null($theme)) {
            $theme = Themes::$plugin->registry->current;
        }
        $uid = $this->type == 'user' ? '' : $this->owner->uid;
        return Themes::$plugin->layouts->get($theme, $this->type, $uid);
    }

    /**
     * Get the url to edit displays for a theme and a view mode.
     * If the theme is null the current theme will be used.
     * 
     * @param  ThemeInterface|string|null    $theme
     * @param  ViewModeInterface|string|null $viewMode
     * @return ?string
     */
    public function getEditDisplaysUrl($theme = null, $viewMode = null): ?string
    {
        return $this->getLayout($theme)->getEditDisplaysUrl($viewMode);
    }

    /**
     * Get the url to edit blocks for a theme.
     * If the theme is null the current theme will be used.
     * 
     * @param  ThemeInterface|string|null    $theme
     * @return string
     */
    public function getEditBlocksUrl($theme = null): string
    {
        return $this->getLayout($theme)->getEditBlocksUrl();
    }
}
