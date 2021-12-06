<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderedOptions;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use craft\fields\Assets;

/**
 * Renders an asset field as rendered using a view mode
 */
class AssetRendered extends AssetLink
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_rendered';

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
        return \Craft::t('themes', 'Rendered as view mode');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return AssetRenderedOptions::class;
    }

    /**
     * Get view modes available, based on the field volumes
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        if ($this->field instanceof UserPhoto) {
            return ViewModesHelper::getUserPhotoViewModes($this->getTheme());
        }
        return ViewModesHelper::getVolumesViewModes($this->field->craftField, $this->getTheme());
    }
}