<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\CategoryGroup;

class CategoryLayout extends Layout
{
    public $type = 'category';
    
    protected function loadElement(): ?CategoryGroup
    {
        $this->_element = \Craft::$app->categories->getGroupByUid($this->element);
        return $this->_element;
    }

    public function getDescription(): string
    {
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->getElement()->name]);
    }
}