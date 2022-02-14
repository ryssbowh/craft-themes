<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\helpers\Assets;

class FileFileOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'displayers' => [
                'field' => 'filedisplayers',
                'mapping' => $this->displayersMapping()
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['displayers', 'validateDisplayers']
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        $displayers = [];
        foreach (Themes::$plugin->fileDisplayers->getDefaults() as $kind => $displayer) {
            $displayer = Themes::$plugin->fileDisplayers->getByHandle($displayer);
            $displayers[$kind]['displayer'] = $displayer->handle;
            $displayers[$kind]['options'] = $displayer->options->defaultValues;
        }
        return ['displayers' => $displayers];
    }

    /**
     * Get the displayer for an asset kind
     * 
     * @param  string $kind
     * @return ?FileDisplayerInterface
     */
    public function getDisplayerForKind(string $kind): ?FileDisplayerInterface
    {
        $displayer = null;
        if ($handle = $this->displayers[$kind]['displayer'] ?? null) {
            $displayer = Themes::$plugin->fileDisplayers->getByHandle($handle);
            $displayer->displayer = $this->displayer;
            if ($options = $this->displayers[$kind]['options'] ?? null) {
                $displayer->options->setValues($options);
            }
        }
        return $displayer;
    }

    /**
     * Validate displayers
     */
    public function validateDisplayers()
    {
        foreach ($this->displayers as $kind => $elem) {
            $displayer = $this->getDisplayerForKind($kind);
            if ($displayer and !$displayer->options->validate()) {
                $this->addError('displayers', [$kind => $displayer->options->getErrors()]);
            }
        }
    }

    /**
     * Maps all displayer and options per asset kind
     * 
     * @return array
     */
    protected function displayersMapping(): array
    {
        $mapping = [];
        foreach ($this->getDisplayer()->getAllowedFileKinds() as $handle => $kind) {
            $mapping[$handle] = [
                'label' => $kind['label'],
                'displayers' => Themes::$plugin->fileDisplayers->getForKind($handle)
            ];
        }
        return $mapping;
    }
}