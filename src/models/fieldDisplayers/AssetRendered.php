<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderedOptions;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use craft\fields\Assets;

/**
 * Renders an asset field as rendered using a view mode
 */
class AssetRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset-rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Assets::class, UserPhoto::class];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $array) {
            foreach ($array['viewModes'] as $uid => $label) {
                $viewMode = Themes::$plugin->viewModes->getByUid($uid);
                //Avoid infinite loops for self referencing view modes :
                if ($viewMode->id != $this->field->viewMode->id) {
                    $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
                }
            }
        }
        return $eagerLoad;
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

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return AssetRenderedOptions::class;
    }
}