<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\FileFileOptions;
use Ryssbowh\CraftThemes\models\fields\File;
use craft\base\Model;
use craft\helpers\Assets;

/**
 * Renders an asset file
 */
class FileFile extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'file_file';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @var array
     */
    protected $_displayerMapping;

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
    public static function getFieldTarget(): String
    {
        return File::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return FileFileOptions::class;
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
     * Get available file kinds
     * 
     * @return array
     */
    public function getAllowedFileKinds(): array
    {
        return Assets::getFileKinds();
    }
}