<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultPlainTextOptions;
use craft\base\Model;
use craft\fields\PlainText;

class DefaultPlainText extends FieldDisplayer
{
    public $handle = 'default_plain_text';

    public $isDefault = true;

    public $name = 'Default';

    public function getName()
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return PlainText::class;
    }

    public function getOptionsModel(): ?Model
    {
        return new DefaultPlainTextOptions($this->field ? $this->field->options : []);
    }
}