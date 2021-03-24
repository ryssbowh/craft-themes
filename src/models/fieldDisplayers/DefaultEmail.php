<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultEmailOptions;
use craft\base\Model;
use craft\fields\Email;

class DefaultEmail extends FieldDisplayer
{
    public $handle = 'email_default';

    public $hasOptions = true;

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Email::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultEmailOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}