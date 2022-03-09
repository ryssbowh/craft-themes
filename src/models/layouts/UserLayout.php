<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\traits\ElementLayout;
use craft\elements\User;
use craft\models\FieldLayout;

/**
 * A layout associated to the user layout and a theme
 */
class UserLayout extends Layout
{
    use ElementLayout;

    /**
     * @inheritDoc
     */
    protected $_type = LayoutService::USER_HANDLE;

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('app', 'User');
    }

    /**
     * @inheritDoc
     */
    public function canHaveBlocks(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getFieldLayout(): ?FieldLayout
    {
        return \Craft::$app->getFields()->getLayoutByType(User::class);
    }

    /**
     * @inheritDoc
     */
    public function getTemplatingKey(): string
    {
        return 'user';
    }
}