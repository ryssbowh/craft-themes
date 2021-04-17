<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultDateOptions;
use craft\base\Model;
use craft\fields\Date;

class DateDefault extends FieldDisplayer
{
    public $handle = 'date_default';

    public $hasOptions = true;

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Date::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultDateOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}