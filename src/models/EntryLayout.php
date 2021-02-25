<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\Section;

class EntryLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'entry';

    /**
     * @inheritDoc
     */
    protected function loadElement(): ?Section
    {
        $this->_element = \Craft::$app->sections->getSectionByUid($this->element);
        return $this->_element;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Entry : {name}', ['name' => $this->getElement()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry_' . $this->getElement()->uid;
    }
}