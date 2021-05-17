<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\PlainTextTrimmedOptions;
use craft\base\Model;
use craft\fields\PlainText;

class PlainTextTrimmed extends FieldDisplayer
{
    public static $handle = 'plain_text_trimmed';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Trimmed');
    }

    public static function getFieldTarget(): String
    {
        return PlainText::class;
    }

    public function getOptionsModel(): Model
    {
        return new PlainTextTrimmedOptions;
    }
}