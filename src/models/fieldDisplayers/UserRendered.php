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
    public static $handle = 'user_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered as view mode');
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
    public function getOptionsModel(): string
    {
        return UserRenderedOptions::class;
    }

    /**
     * Get the layout associated to users
     * 
     * @return LayoutInterface
     */
    public function getUserLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->getTheme(), LayoutService::USER_HANDLE);
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
}