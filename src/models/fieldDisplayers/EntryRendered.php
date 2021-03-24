<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\EntryRenderedOptions;
use craft\base\Model;
use craft\fields\Entries;

class EntryRendered extends FieldDisplayer
{
    public $handle = 'entry_rendered';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    public function getFieldTarget(): String
    {
        return Entries::class;
    }

    public function getOptionsModel(): Model
    {
        return new EntryRenderedOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}