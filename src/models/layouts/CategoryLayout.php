<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;

class CategoryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::CATEGORY_HANDLE;
    
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['element', 'required'],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->categories->getGroupByUid($this->element);
    }

    public function getElementMachineName(): string
    {
        return $this->element()->handle;
    }

    public function hasDisplays(): bool
    {
        return true;
    }

    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->element()->handle . '_' . $this->theme);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->element()->name]);
    }

    public function getCraftFields(): array
    {
        return $this->element()->getFieldLayout()->getFields();
    }
}