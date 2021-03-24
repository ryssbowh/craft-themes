<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EntryLinkOptions extends FieldDisplayerOptions
{
    public $label = 'title';
    public $custom = '';
    public $newTab = false;

    public function getLabelsOptions(): array
    {
        return [
            'title' => \Craft::t('themes', 'Entry title'), 
            'custom' => \Craft::t('themes', 'Custom') 
        ];
    }

    public function rules()
    {
        return [
            [['label', 'custom'], 'string'],
            ['newTab', 'boolean'],
            ['label', 'in', 'range' => array_keys($this->getLabelsOptions())]
        ];
    }
}