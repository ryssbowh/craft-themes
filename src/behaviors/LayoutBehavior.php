<?php
namespace Ryssbowh\CraftThemes\behaviors;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use yii\base\Behavior;

class LayoutBehavior extends Behavior
{
    public $owner;

    public $type;

    public function getLayout($theme): ?LayoutInterface
    {
        return Themes::$plugin->layouts->get($theme, $this->type, $this->owner->uid);
    }

    public function getCurrentThemeLayout(): ?LayoutInterface
    {
        $theme = Themes::$plugin->registry->current;
        return $theme ? $this->getLayout($theme) : null;
    }
}
