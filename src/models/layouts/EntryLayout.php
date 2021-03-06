<?php

namespace Ryssbowh\CraftThemes\models\layouts;

class EntryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'entry';

    /**
     * @var boolean
     */
    protected $_hasFields = true;

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
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Entry : {name}', ['name' => $this->element()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry_' . $this->element;
    }
}