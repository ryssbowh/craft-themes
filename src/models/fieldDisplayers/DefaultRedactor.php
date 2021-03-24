<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultRedactorOptions;
use craft\base\Model;
use craft\redactor\Field;

class DefaultRedactor extends FieldDisplayer
{
    public $handle = 'redactor_default';

    public $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Field::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultRedactorOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}