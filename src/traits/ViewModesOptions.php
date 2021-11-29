<?php
namespace Ryssbowh\CraftThemes\traits;

/**
 * Trait for displayer options that needs view modes for several elements.
 * The view modes must be defined by the displayer
 */
trait ViewModesOptions
{
    /**
     * @return array
     */
    public function defineViewModesOptions(): array
    {
        $viewModes = $this->displayer->getViewModes();
        return [
            'viewModes' => [
                'field' => 'viewmodes',
                'options' => $viewModes,
                'warning' => sizeof($viewModes) == 0 ? \Craft::t('themes', "It seems this field doesn't have any valid source") : null
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineViewModesRules(): array
    {
        return [
            ['viewModes', 'validateViewModes', 'skipOnEmpty' => false]
        ];
    }

    /**
     * View modes setter
     * 
     * @param array $viewModes
     */
    public function setViewModes(array $viewModes)
    {
        $this->_viewModes = $viewModes;
    }

    /**
     * Validate view modes
     */
    public function validateViewModes()
    {
        $validViewModes = $this->displayer->getViewModes();
        foreach ($this->viewModes as $uid => $viewModeUid) {
            if (!isset($validViewModes[$uid]['viewModes'][$viewModeUid])) {
                $this->addError('viewModes', [$uid => \Craft::t('themes', 'View mode is invalid')]);
            }
        }
    }
}