<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\EntryLinkOptions;
use craft\base\Model;
use craft\fields\Entries;

class EntryLink extends FieldDisplayer
{
    public $handle = 'entry_link';

    public $hasOptions = true;

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Link to entry');
    }

    public function getFieldTarget(): String
    {
        return Entries::class;
    }

    public function getOptionsModel(): Model
    {
        return new EntryLinkOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/'.$this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}