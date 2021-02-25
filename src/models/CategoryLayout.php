<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\CategoryGroup;

class CategoryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'category';
        
    /**
     * @inheritDoc
     */
    protected function loadElement(): ?CategoryGroup
    {
        $this->_element = \Craft::$app->categories->getGroupByUid($this->element);
        return $this->_element;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->getElement()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'category_' . $this->getElement()->uid;
    }
}