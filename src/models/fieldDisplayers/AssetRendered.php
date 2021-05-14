<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\AssetRenderedOptions;
use craft\base\Model;
use craft\fields\Assets;

class AssetRendered extends FieldDisplayer
{
    public $handle = 'asset_rendered';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    public function getFieldTarget(): String
    {
        return Assets::class;
    }

    public function getOptionsModel(): Model
    {
        return new AssetRenderedOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}