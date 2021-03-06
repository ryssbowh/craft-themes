<?php

namespace Ryssbowh\CraftThemes\models\layouts;

class CategoryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'category';

    /**
     * @var boolean
     */
    protected $_hasFields = true;
        
    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->categories->getGroupByUid($this->element);
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
        return 'category_' . $this->element;
    }
}