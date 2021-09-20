<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
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
            ['elementUid', 'required'],
        ]);
    }
    
    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        foreach (\Craft::$app->sections->getAllEntryTypes() as $entryType) {
            if ($entryType->uid == $this->elementUid) {
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
    public function getElementMachineName(): string
    {
        return $this->element->handle;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Section {section} : {name}', ['section' => $this->element->section->name, 'name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return $this->element->getFieldLayout()->getFields();
    }
}