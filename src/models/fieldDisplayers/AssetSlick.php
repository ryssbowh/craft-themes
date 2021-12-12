<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;
use craft\fields\Assets;

/**
 * Renders an asset field as a slick carousel
 */
class AssetSlick extends AssetRendered
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_slick';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Slick Carousel');
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
    protected function getOptionsModel(): string
    {
        return AssetSlickOptions::class;
    }
}