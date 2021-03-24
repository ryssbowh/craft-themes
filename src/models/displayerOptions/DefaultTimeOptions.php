<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultTimeOptions extends FieldDisplayerOptions
{
    public $format = 'H:i:s';
    public $custom = '';

    public function getFormatOptions(): array
    {
        return [
            'H:i:s' => 'Full : 13:25:36',
            'H:i' => 'Without seconds : 13:25',
            'custom' => \Craft::t('themes', 'Custom') 
        ];
    }

    public function rules()
    {
        return [
            [['format', 'custom'], 'string']
        ];
    }
}