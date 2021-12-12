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
    public function defineViewModesDefaultValues(): array
    {
        $viewModes = $this->displayer->getViewModes();
        $options = [];
        foreach ($viewModes as $id => $array) {
            $keys = array_keys($array['viewModes'] ?? []);
            $options['viewMode-' . $id] = $keys[0] ?? null;
        }
        return $options;
    }

    /**
     * @return array
     */
    public function defineViewModesOptions(): array
    {
        $viewModes = $this->displayer->getViewModes();
        $options = [];
        foreach ($viewModes as $id => $array) {
            $array['viewModes'] = [
                '' => 'None (skip display)'
            ] + $array['viewModes'];
            $options['viewMode-' . $id] = [
                'field' => 'select',
                'required' => true,
                'label' => \Craft::t('themes', 'View mode for {type}', ['type'=> $array['label']]),
                'options' => $array['viewModes']
            ];
        }
        return $options;
    }

    /**
     * @inheritDoc
     */
    public function defineViewModesRules(): array
    {
        $viewModes = $this->displayer->getViewModes();
        $rules = [];
        foreach ($viewModes as $id => $array) {
            $viewMode = 'viewMode-' . $id;
            $rules[] = [$viewMode, 'in', 'range' => array_keys($this->definitions[$viewMode]['options']), 'message' => \Craft::t('themes', 'View mode is invalid')];
            $rules[] = [$viewMode, function () use ($viewMode) {
                if ($this->displayer->field->viewMode->uid == $this->$viewMode) {
                    $this->addError($viewMode, \Craft::t('themes', 'View modes can\'t reference themselves'));
                }
            }];
        }
        return $rules;
    }
}