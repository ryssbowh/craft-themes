<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\models\FieldLayout;

class TagLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::TAG_HANDLE;

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->tags->getTagGroupByUid($this->elementUid);
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
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Tag : {name}', ['name' => $this->element->name]);
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
        return $this->element->getFieldLayout();
    }

    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
    {
        return 'tag';
    }
}