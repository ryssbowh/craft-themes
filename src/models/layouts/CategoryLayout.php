<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\helpers\ElementLayoutTrait;
use Ryssbowh\CraftThemes\services\LayoutService;

class CategoryLayout extends Layout
{
    use ElementLayoutTrait;

    /**
     * @var string
     */
    protected $_type = LayoutService::CATEGORY_HANDLE;

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
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Category : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->categories->getGroupByUid($this->elementUid);
    }
}