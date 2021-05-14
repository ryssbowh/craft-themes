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

    public function hasDisplays(): bool
    {
        return true;
    }

    public function canHaveUrls(): bool
    {
        return false;
    }

    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->theme);
    }

    public function getCraftFields(): array
    {
        return \Craft::$app->getFields()->getLayoutByType(User::class)->getFields();
    }
}