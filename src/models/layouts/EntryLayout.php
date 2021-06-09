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

    /**
     * @inheritDoc
     */
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
    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->element()->handle . '_' . $this->theme);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return $this->element()->getFieldLayout()->getFields();
    }
}