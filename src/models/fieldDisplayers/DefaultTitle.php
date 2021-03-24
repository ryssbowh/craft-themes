<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\DefaultTitleOptions;
use craft\base\Model;
use craft\fieldlayoutelements\TitleField;

class DefaultTitle extends FieldDisplayer
{
    public $handle = 'title_default';

    public $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return TitleField::class;
    }

    public function getOptionsModel(): Model
    {
        return new DefaultTitleOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}