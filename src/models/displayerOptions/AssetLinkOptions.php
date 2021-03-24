<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AssetLinkOptions extends FieldDisplayerOptions
{
    public $label = 'title';
    public $custom = '';
    public $newTab = false;
    public $download = false;

    public function getLabelsOptions(): array
    {
        return [
            'title' => \Craft::t('themes', 'Asset title'), 
            'custom' => \Craft::t('themes', 'Custom') 
        ];
    }

    public function rules()
    {
        return [
            [['label', 'custom'], 'string'],
            [['newTab', 'download'], 'boolean'],
            ['label', 'in', 'range' => array_keys($this->getLabelsOptions)]
        ];
    }
}