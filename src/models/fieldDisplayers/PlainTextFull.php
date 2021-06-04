<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PlainTextFullOptions;
use craft\base\Model;
use craft\fields\PlainText;

class PlainTextFull extends FieldDisplayer
{
    public static $handle = 'plain_text_full';

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Full');
    }

    public static function getFieldTarget(): String
    {
        return PlainText::class;
    }
}