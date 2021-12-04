<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderedOptions;
use craft\fields\Assets;

/**
 * Renders an asset field as rendered using a view mode
 */
class AssetRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered as view mode');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Assets::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return AssetRenderedOptions::class;
    }
}