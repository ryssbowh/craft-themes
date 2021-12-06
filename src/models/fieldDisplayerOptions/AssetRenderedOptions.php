<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
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
    public function defineDefaultValues(): array
    {
        return $this->defineViewModesDefaultValues();
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return $this->defineViewModesRules();
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return $this->defineViewModesOptions();
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
            $viewModeUid = $this->getValue('viewMode-' . $volume->uid);
            if ($viewModeUid) {
                try {
                    return Themes::$plugin->viewModes->getByUid($viewModeUid);
                } catch (ViewModeException $e) {
                    return null;
                }
            }
        }
        return null;
    }
}