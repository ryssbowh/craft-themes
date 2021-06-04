<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\helpers\Assets;

class FileDefaultOptions extends FieldDisplayerOptions
{
    public $displayers = [];

    public function defineRules(): array
    {
        return [
            ['displayers', 'validateDisplayers']
        ];
    }

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

    public function getOptionsForDisplayer(string $kind, string $displayer): array
    {
        if (isset($this->displayers[$kind]) and $this->displayers[$kind]['displayer'] == $displayer) {
            return $this->displayers[$kind]['options'] ?? [];
        }
        return [];
    }

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