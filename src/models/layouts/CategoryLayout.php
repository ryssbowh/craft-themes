<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;

class CategoryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::CATEGORY_HANDLE;

    /**
     * @var boolean
     */
    protected $_hasDisplays = true;
        
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

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->element()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->type . '_' . $this->element;
    }
}