<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\ViewModesOptions;
use craft\elements\Asset;

class AssetRenderedOptions extends FieldDisplayerOptions
{
    use ViewModesOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), $this->defineViewModesRules());
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return array_merge(parent::defineOptions(), $this->defineViewModesOptions());
    }

    /**
     * Get the view mode for an asset
     * 
     * @param  Asset  $asset
     * @return ?ViewModeInterface
     */
    public function getViewMode(Asset $asset): ?ViewModeInterface
    {
        $volume = $asset->volume;
        if ($volume) {
            $viewModeUid = $this->getViewModes()[$volume->uid] ?? null;
            return $viewModeUid ? Themes::$plugin->viewModes->getByUid($viewModeUid) : null;
        }
        return null;
    }
}