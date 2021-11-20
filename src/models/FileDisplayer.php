<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\NoOptions;
use craft\base\Model;
use craft\elements\Asset;

/**
 * Base class for all file displayers
 */
abstract class FileDisplayer extends Model implements FileDisplayerInterface
{
    /**
     * @var boolean
     */
    public static $isDefault = false;
    
    /**
     * @var FieldDisplayerInterface
     */
    protected $_displayer;
        
    /**
     * @var boolean
     */
    public $hasOptions = false;

    /**
     * @var Model
     */
    protected $_options;

    /**
     * @inheritDoc
     */
    public function getHandle(): string 
    {
        return $this::$handle;
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
    public function getOptions(): Model
    {
        if ($this->_options === null) {
            $model = $this->getOptionsModel();
            $model->displayer = $this;
            $this->_options = $model;
        }
        return $this->_options;
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
        return array_merge(parent::fields(), ['name', 'options', 'handle']);
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(Asset $asset): bool
    {
        return true;
    }
}