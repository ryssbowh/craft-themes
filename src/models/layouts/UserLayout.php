<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\helpers\ElementLayoutTrait;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\models\FieldLayout;

class UserLayout extends Layout
{
    use ElementLayoutTrait;

    /**
     * @var string
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
    public function getElementMachineName(): string
    {
        return 'user';
    }
}