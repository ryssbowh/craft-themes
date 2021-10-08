<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\helpers\ElementLayoutTrait;
use Ryssbowh\CraftThemes\services\LayoutService;

class CustomLayout extends Layout
{
    /**
     * @var string
     */
    protected $_type = LayoutService::CUSTOM_HANDLE;

    /**
     * @inheritDoc
     */
    public function getIsCustom(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['elementUid', 'required'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
    {
        return $this->elementUid;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Custom : {name}', ['name' => $this->name]);
    }
}