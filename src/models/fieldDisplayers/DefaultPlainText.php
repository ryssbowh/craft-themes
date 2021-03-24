<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultPlainTextOptions;
use craft\base\Model;
use craft\fields\PlainText;

class DefaultPlainText extends FieldDisplayer
{
    public $handle = 'plain_text_default';

    public $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return PlainText::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultPlainTextOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}