<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\fields\Assets;

class AssetRendered extends FieldDisplayer
{
    public static $handle = 'asset_rendered';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    public static function getFieldTarget(): String
    {
        return Assets::class;
    }

    public function getOptionsModel(): Model
    {
        return new AssetRenderedOptions;
    }

    public function getViewModes(): array
    {
        $source = $this->field->craftField->sources;
        $viewModes = [];
        if ($source == '*') {
            $volumes = \Craft::$app->volumes->getAllVolumes();
        } else {
            $volumes = [];
            foreach ($source as $source) {
                $elems = explode(':', $source);
                $volumes[] = \Craft::$app->volumes->getVolumeByUid($elems[1]);        
            }
        }
        foreach ($volumes as $volume) {
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

    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }
}