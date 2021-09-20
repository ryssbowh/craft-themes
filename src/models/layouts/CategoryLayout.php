<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\models\FieldLayout;

class CategoryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::CATEGORY_HANDLE;
    
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
    protected function loadElement()
    {
        return \Craft::$app->categories->getGroupByUid($this->elementUid);
    }

    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
    {
        return $this->element->handle;
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
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return $this->element->getFieldLayout()->getFields();
    }
}