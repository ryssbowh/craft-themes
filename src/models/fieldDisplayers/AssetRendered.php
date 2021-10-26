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
            if (!$volume) {
                continue;
            }
            $layout = $volume->getLayout($this->getTheme());
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
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }
}