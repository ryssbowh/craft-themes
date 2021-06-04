<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetLinkOptions;
use craft\base\Model;
use craft\fields\Assets;

class AssetLink extends FieldDisplayer
{
    public static $handle = 'asset_link';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Link to asset');
    }

    public static function getFieldTarget(): String
    {
        return Assets::class;
    }

    public function getOptionsModel(): Model
    {
        return new AssetLinkOptions;
    }
}