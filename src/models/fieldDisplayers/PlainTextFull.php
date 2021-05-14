<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\PlainTextFullOptions;
use craft\base\Model;
use craft\fields\PlainText;

class PlainTextFull extends FieldDisplayer
{
    public $handle = 'plain_text_full';

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Full');
    }

    public function getFieldTarget(): String
    {
        return PlainText::class;
    }
}