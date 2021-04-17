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
    public function getHandle(): string
    {
        return $this->type . '_' . $this->element;
    }

    public function getFieldLayout(): FieldLayout
    {
        return $this->element()->getFieldLayout();
    }
}