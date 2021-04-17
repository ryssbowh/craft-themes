<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\models\FieldLayout;

class UserLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::USER_HANDLE;

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'User');
    }

    public function hasDisplays(): bool
    {
        return true;
    }

    public function canHaveUrls(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return LayoutService::USER_HANDLE;
    }

    public function getFieldLayout(): FieldLayout
    {
        return \Craft::$app->getFields()->getLayoutByType(User::class);
    }
}