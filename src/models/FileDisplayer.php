<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use craft\base\Model;
use craft\elements\Asset;

/**
 * Base class for all file displayers
 */
abstract class FileDisplayer extends Model implements FileDisplayerInterface
{
    /**
     * @var FieldDisplayerInterface
     */
    protected $_displayer;

    /**
     * @var FileDisplayerOptions
     */
    protected $_options;

    /**
     * @inheritDoc
     */
    public static function isDefault(string $kind): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Hello';
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    public static function getHandle(): string 
    {
        return static::$handle;
    }

    /**
     * @inheritDoc
     */
    public function setDisplayer(FieldDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayer(): FieldDisplayerInterface
    {
        return $this->_displayer;
    }

    /**
     * @inheritDoc
     */
    public function getField(): FieldInterface
    {
        return $this->displayer->field;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): FileDisplayerOptions
    {
        if ($this->_options === null) {
            $class = $this->getOptionsModel();
            $this->_options = new $class([
                'displayer' => $this
            ]);
        }
        return $this->_options;
    }

    /**
     * @inheritDoc
     */
    public function getHasOptions(): bool
    {
        return sizeof($this->options->definitions) > 0;
    }

    /**
     * Get theme associated to this displayer
     * 
     * @return ThemeInterface
     */
    public function getTheme()
    {
        return $this->displayer->field->layout->theme;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'options', 'handle', 'hasOptions', 'description']);
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(Asset $asset): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return $this->displayer->canBeCached;
    }

    /**
     * Get options model class
     * 
     * @return string
     */
    abstract protected function getOptionsModel(): string;
}