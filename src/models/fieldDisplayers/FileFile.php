<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\FileFileOptions;
use Ryssbowh\CraftThemes\models\fields\File;
use craft\helpers\Assets;

/**
 * Renders an asset file
 */
class FileFile extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'file-file';

    /**
     * @var array
     */
    protected $_displayerMapping;

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'File');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [File::class];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->options->displayers as $kind => $options) {
            if ($displayer = $this->options->getDisplayerForKind($kind)) {
                $eagerLoad = $displayer->eagerLoad($eagerLoad, $prefix, $level);
            }
        }
        return $eagerLoad;
    }

    /**
     * Get available file kinds
     * 
     * @return array
     */
    public function getAllowedFileKinds(): array
    {
        return Assets::getFileKinds();
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return FileFileOptions::class;
    }
}