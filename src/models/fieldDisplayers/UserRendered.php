<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Renders a user field as rendered using a view mode
 */
class UserRendered extends UserDefault
{
    /**
     * @inheritDoc
     */
    public static $handle = 'user-rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $label) {
            $viewMode = Themes::$plugin->viewModes->getByUid($uid);
            //Avoid infinite loops for self referencing view modes :
            if ($viewMode->id != $this->field->viewMode->id) {
                $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
            }
        }
        return $eagerLoad;
    }

    /**
     * Get the layout associated to users
     * 
     * @return LayoutInterface
     */
    public function getUserLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->getTheme(), 'user');
    }

    /**
     * Get view modes available, based on the field users
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getUserViewModes($this->getTheme());
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return UserRenderedOptions::class;
    }
}