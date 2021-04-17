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
    protected function loadElement()
    {
        foreach (\Craft::$app->globals->getAllSets() as $set) {
            if ($set->uid == $this->element) {
                return $set;
            }
        }
        return null;
    }

    public function hasDisplays(): bool
    {
        return true;
    }


    public function canHaveUrls(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Global : {name}', ['name' => $this->element()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->type . '_' . $this->element;
    }

    public function getFieldLayout(): FieldLayout
    {
        return $this->element()->getFieldLayout();
    }
}