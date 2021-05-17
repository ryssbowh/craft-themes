<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\helpers\Assets;

class FileDefaultOptions extends FieldDisplayerOptions
{
    public $displayers = [];

    public function defineRules(): array
    {
        return [
            ['displayers', 'safe']
        ];
    }

    public function getDisplayersMapping()
    {
        $out = [];
        foreach (Assets::getFileKinds() as $handle => $kind) {
            $out[$handle] = [
                'label' => $kind['label'],
                'displayers' => Themes::$plugin->fileDisplayers->getForKind($handle)
            ];
        }
        return $out;
    }
}