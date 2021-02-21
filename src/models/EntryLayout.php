<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\Section;

class EntryLayout extends Layout
{
    public $type = 'entry';

    protected function loadElement(): ?Section
    {
        $this->_element = \Craft::$app->sections->getSectionByUid($this->element);
        return $this->_element;
    }

    public function getDescription(): string
    {
        return \Craft::t('themes', 'Entry : {name}', ['name' => $this->getElement()->name]);
    }
}