<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetLinkOptions;
use craft\fields\Assets;

/**
 * Renders an asset field as link
 */
class AssetLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_link';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Link to asset');
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
        return AssetLinkOptions::class;
    }
}