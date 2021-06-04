<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\FileDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\File;
use craft\base\Model;
use craft\helpers\Assets;

class FileDefault extends FieldDisplayer
{
    public static $handle = 'file_default';

    public $hasOptions = true;

    public static $isDefault = true;

    protected $_displayerMapping;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return File::class;
    }

    public function getOptionsModel(): Model
    {
        return new FileDefaultOptions;
    }

    public function getDisplayerForKind(string $kind): ?FileDisplayerInterface
    {
        return $this->options->getDisplayerForKind($kind);
    }

    public function getDisplayersMapping(): array
    {
        if ($this->_displayerMapping === null) {
            $mapping = [];
            foreach (Assets::getFileKinds() as $handle => $kind) {
                $displayers = Themes::$plugin->fileDisplayers->getForKind($handle);
                foreach ($displayers as $displayer) {
                    if ($options = $this->options->getOptionsForDisplayer($handle, $displayer::$handle)) {
                        $displayer->options->setAttributes($options);
                    }
                }
                $mapping[$handle] = [
                    'label' => $kind['label'],
                    'displayers' => $displayers
                ];
            }
            $this->_displayerMapping = $mapping;
        }
        return $this->_displayerMapping;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['displayersMapping']);
    }
}