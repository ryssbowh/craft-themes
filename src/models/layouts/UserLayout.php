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
        return \Craft::t('app', 'User');
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
        return $this->fieldLayout->getFields();
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
    public function getElementMachineName(): string
    {
        return 'user';
    }
}