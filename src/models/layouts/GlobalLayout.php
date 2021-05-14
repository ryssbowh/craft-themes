<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;

class GlobalLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::GLOBAL_HANDLE;

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

    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->element()->handle . '_' . $this->theme);
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

    public function getCraftFields(): array
    {
        return $this->element()->getFieldLayout()->getFields();
    }
}