<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultNumberOptions;
use craft\base\Model;
use craft\fields\Number;

class DefaultNumber extends FieldDisplayer
{
    public $handle = 'number_default';

    public $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Number::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultNumberOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}