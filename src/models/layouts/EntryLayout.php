<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;

class EntryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::ENTRY_HANDLE;

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
        foreach (\Craft::$app->sections->getAllEntryTypes() as $entryType) {
            if ($entryType->uid == $this->element) {
                return $entryType;
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

    public function getElementMachineName(): string
    {
        return $this->element()->handle;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Entry : {name}', ['name' => $this->element()->name]);
    }

    public function getCraftFields(): array
    {
        return $this->element()->getFieldLayout()->getFields();
    }
}