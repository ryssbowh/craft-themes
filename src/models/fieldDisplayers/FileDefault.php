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
    /**
     * @inheritDoc
     */
    public static $handle = 'file_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @var array
     */
    protected $_displayerMapping;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return File::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return FileDefaultOptions::class;
    }

    /**
     * Get the displayer defined for an asset kind
     * 
     * @param  string $kind
     * @return ?FileDisplayerInterface
     */
    public function getDisplayerForKind(string $kind): ?FileDisplayerInterface
    {
        return $this->options->getDisplayerForKind($kind);
    }

    /**
     * Get available displayers, indexed by asset kind
     * 
     * @return array
     */
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

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['displayersMapping']);
    }
}