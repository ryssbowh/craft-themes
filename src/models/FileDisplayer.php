<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\NoOptions;
use craft\base\Model;

abstract class FileDisplayer extends Model implements FileDisplayerInterface
{
    protected $_displayer;
    
    public $hasOptions = false;

    protected $_options;

    public function getHandle(): string 
    {
        return $this::$handle;
    }

    public function setDisplayer($displayer)
    {
        $this->_displayer = $displayer;
    }

    public function getDisplayer(): FieldDisplayerInterface
    {
        return $this->_displayer;
    }

    public function getOptions(): Model
    {
        if ($this->_options === null) {
            $model = $this->getOptionsModel();
            $model->displayer = $this;
            // $model->setAttributes($this->displayer->field->options, false);
            $this->_options = $model;
        }
        return $this->_options;
    }

    public function getTheme()
    {
        return $this->displayer->field->layout->theme;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'options', 'handle']);
    }

    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }
}