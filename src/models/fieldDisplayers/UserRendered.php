<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserRenderedOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\fields\Users;

/**
 * Renders a user field as rendered using a view mode
 */
class UserRendered extends FieldDisplayer
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
    public static function getFieldTargets(): array
    {
        return [Author::class, UserInfo::class, Users::class];
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
}