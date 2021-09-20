<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\models\FieldLayout;

class GlobalLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::GLOBAL_HANDLE;

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
        foreach (\Craft::$app->globals->getAllSets() as $set) {
            if ($set->uid == $this->elementUid) {
                return $set;
            }
        }
        return null;
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
    public function canHaveUrls(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Global : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return $this->element->getFieldLayout()->getFields();
    }
}