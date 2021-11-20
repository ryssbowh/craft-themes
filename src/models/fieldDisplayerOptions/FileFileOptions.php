<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\helpers\Assets;

class FileFileOptions extends FieldDisplayerOptions
{
    /**
     * Displayers, indexed by asset kind
     * @var array
     */
    public $displayers;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['displayers', 'validateDisplayers']
        ];
    }

    public function init()
    {
        parent::init();
        if ($this->displayers === null) {
            $this->displayers = [];
            foreach (Themes::$plugin->fileDisplayers->getDefaults() as $kind => $displayer) {
                $this->displayers[$kind]['displayer'] = $displayer;
            }
        }
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
        if ($this->displayers[$kind] ?? null) {
            $displayer = Themes::$plugin->fileDisplayers->getByHandle($this->displayers[$kind]['displayer']);
        } else {
            $displayer = Themes::$plugin->fileDisplayers->getForKind($kind)[0];
        }
        if ($options = $this->getOptionsForDisplayer($kind, $displayer::$handle)) {
            $displayer->options->setAttributes($options, false);
        }
        return $displayer;
    }

    /**
     * Get options for an asset kind and a displayer
     * 
     * @param  string $kind
     * @param  string $displayer
     * @return array
     */
    public function getOptionsForDisplayer(string $kind, string $displayer): array
    {
        if (isset($this->displayers[$kind]) and $this->displayers[$kind]['displayer'] == $displayer) {
            return $this->displayers[$kind]['options'] ?? [];
        }
        return [];
    }

    /**
     * Validate displayers
     */
    public function validateDisplayers()
    {
        foreach ($this->displayers as $kind => $elem) {
            $displayer = $this->getDisplayerForKind($kind);
            if ($displayer and !$displayer->options->validate()) {
                $this->addError($kind, $displayer->options->getErrors());
            }
        }
    }
}