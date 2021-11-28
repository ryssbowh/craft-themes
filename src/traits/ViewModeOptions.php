<?php
namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Trait for displayer options that needs a view mode
 */
trait ViewModeOptions
{
    /**
     * Define the default value for the viewModeUid option
     * 
     * @return array
     */
    public function defineViewModeDefaultValues(): array
    {
        $viewModes = array_keys($this->getDisplayer()->getViewModes());
        return [
            'viewModeUid' => $viewModes[0] ?? null
        ];
    }

    /**
     * Get the view mode
     * 
     * @return ?ViewModeInterface
     */
    public function getViewMode(): ?ViewModeInterface
    {
        if ($this->viewModeUid) {
            return Themes::$plugin->viewModes->getByUid($this->viewModeUid);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function defineViewModeRules(): array
    {
        return [
            ['viewModeUid', 'validateViewMode', 'skipOnEmpty' => false]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineViewModeOptions(): array
    {
        $viewModes = $this->getDisplayer()->getViewModes();
        return [
            'viewModeUid' => [
                'field' => 'select',
                'options' => $viewModes,
                'label' => \Craft::t('themes', 'View mode'),
                'required' => true,
                'warning' => sizeof($viewModes) == 0 ? \Craft::t('themes', "It seems this field doesn't have any valid source") : null
            ]
        ];
    }

    /**
     * Validate view mode
     */
    public function validateViewMode()
    {
        if (!isset($this->displayer->getViewModes()[$this->viewModeUid])) {
            $this->addError('viewModeUid', \Craft::t('themes', 'View mode is invalid'));
        }
    }
}