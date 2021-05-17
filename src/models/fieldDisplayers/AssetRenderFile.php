<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\AssetRenderFileOptions;
use craft\base\Model;
use craft\fields\Assets;

class AssetRenderFile extends FieldDisplayer
{
    public static $handle = 'asset_render_file';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Render file');
    }

    public function getFieldTarget(): String
    {
        return Assets::class;
    }

    public function getOptionsModel(): Model
    {
        return new AssetRenderFileOptions;
    }
}