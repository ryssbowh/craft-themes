<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\AssetLinkOptions;
use craft\base\Model;
use craft\fields\Assets;

class AssetLink extends FieldDisplayer
{
    public $handle = 'asset_link';

    public $hasOptions = true;

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Link to asset');
    }

    public function getFieldTarget(): String
    {
        return Assets::class;
    }

    public function getOptionsModel(): Model
    {
        return new AssetLinkOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }
}