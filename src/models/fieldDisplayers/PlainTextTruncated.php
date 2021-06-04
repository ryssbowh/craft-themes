<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PlainTextTruncatedOptions;
use craft\base\Model;
use craft\fields\PlainText;

class PlainTextTruncated extends FieldDisplayer
{
    public static $handle = 'plain_text_truncated';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Truncated');
    }

    public static function getFieldTarget(): String
    {
        return PlainText::class;
    }

    public function getOptionsModel(): Model
    {
        return new PlainTextTruncatedOptions;
    }
}