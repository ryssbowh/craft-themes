<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderFileOptions;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use craft\fields\Assets;
use craft\helpers\Assets as AssetsHelper;

/**
 * Renders the file of an asset field
 */
class AssetRenderFile extends AssetLink
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_render_file';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

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
        if ($this->field instanceof UserPhoto) {
            return ['image' => $kinds['image']];
        }
        if ($this->field->craftField->restrictFiles) {
            $allowed = $this->field->craftField->allowedKinds;
            $kinds = array_filter($kinds, function ($key) use ($allowed) {
                return in_array($key, $allowed);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $kinds;
    }
}