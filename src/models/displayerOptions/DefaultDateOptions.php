<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultDateOptions extends FieldDisplayerOptions
{
    public $format = 'd/m/Y H:i:s';
    public $custom = '';

    public function getFormatOptions(): array
    {
        return [
            'd/m/Y H:i:s' => 'Full : 31/10/2005 13:25:13',
            'd/m/Y' => 'Date : 31/10/2005',
            'H:i' => 'Time : 13:25',
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