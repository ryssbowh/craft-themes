<?php
namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
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
        $viewModes = array_keys($this->displayer->getViewModes());
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
            try {
                return Themes::$plugin->viewModes->getByUid($this->viewModeUid);
            } catch(ViewModeException $e) {
                return null;
            }
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
        $viewModes = $this->displayer->getViewModes();
        return [
            'viewModeUid' => [
                'field' => 'select',
                'options' => [
                    '' => 'None (skip display)'
                ] + $viewModes,
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
        if ($this->viewModeUid and !isset($this->displayer->getViewModes()[$this->viewModeUid])) {
            $this->addError('viewModeUid', \Craft::t('themes', 'View mode is invalid'));
        }
        if ($this->viewModeUid and $this->viewModeUid == $this->displayer->field->viewMode->uid) {
            $this->addError('viewModeUid', \Craft::t('themes', 'View modes can\'t reference themselves'));   
        }
    }
}