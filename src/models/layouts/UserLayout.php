<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\helpers\StringHelper;
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

    /**
     * @inheritDoc
     */
    public function hasDisplays(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canHaveUrls(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return \Craft::$app->getFields()->getLayoutByType(User::class)->getFields();
    }

    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
    {
        return 'user';
    }
}