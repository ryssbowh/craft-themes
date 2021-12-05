<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderFileOptions;
use craft\fields\Assets;
use craft\helpers\Assets as AssetsHelper;

/**
 * Renders the file of an asset field
 */
class AssetRenderFile extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_render_file';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Render file');
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
        return AssetRenderFileOptions::class;
    }

    /**
     * Get available file kinds
     * 
     * @return array
     */
    public function getAllowedFileKinds(): array
    {
        $kinds = AssetsHelper::getFileKinds();
        if ($this->field->craftField->restrictFiles) {
            $allowed = $this->field->craftField->allowedKinds;
            $kinds = array_filter($kinds, function ($key) use ($allowed) {
                return in_array($key, $allowed);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $kinds;
    }
}