<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\elements\Asset;
use craft\fields\Assets;

class AssetRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_rendered';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

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
    public static function getFieldTarget(): string
    {
        return Assets::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return AssetRenderedOptions::class;
    }

    /**
     * Get all defined volumes on the field
     * 
     * @return array
     */
    public function getAllVolumes(): array
    {
        $source = $this->field->craftField->sources;
        if ($source == '*') {
            return \Craft::$app->volumes->getAllVolumes();
        } else {
            $volumes = [];
            foreach ($source as $source) {
                $elems = explode(':', $source);
                $volumes[] = \Craft::$app->volumes->getVolumeByUid($elems[1]);        
            }
            return $volumes;
        }
    }

    /**
     * Get view modes for the defined source volume of the associated field
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $viewModes = [];
        foreach ($this->allVolumes as $volume) {
            $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::VOLUME_HANDLE, $volume->uid);
            $volumeViewModes = [];
            foreach ($layout->viewModes as $viewMode) {
                $volumeViewModes[$viewMode->uid] = $viewMode->name;
            }
            $viewModes[$volume->uid] = [
                'label' => $volume->name,
                'viewModes' => $volumeViewModes
            ];
        }
        return $viewModes;
    }

    /**
     * Get the layout for an asset
     * 
     * @param  Asset  $asset
     * @return LayoutInterface
     */
    public function getVolumeLayout(Asset $asset): LayoutInterface
    {
        $volume = $asset->volume;
        $theme = Themes::$plugin->registry->getCurrent();
        $layout = Themes::$plugin->layouts->get($theme->handle, LayoutService::VOLUME_HANDLE, $volume->uid);
        return $layout;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }
}