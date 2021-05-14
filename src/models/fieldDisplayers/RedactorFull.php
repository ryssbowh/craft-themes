<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\RedactorFullOptions;
use craft\base\Model;
use craft\redactor\Field;

class RedactorFull extends FieldDisplayer
{
    public $handle = 'redactor_full';

    public $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Full');
    }

    public function getFieldTarget(): String
    {
        return Field::class;
    }

    public function getOptionsModel(): Model
    {
        return new RedactorFullOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}