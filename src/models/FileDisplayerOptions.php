<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use craft\base\Model;

/**
 * Base class for all file displayers options
 */
class FileDisplayerOptions extends Model
{
    /**
     * @var FileDisplayerInterface
     */
    protected $_displayer;

    /**
     * File displayer getter
     * 
     * @return FileDisplayerInterface
     */
    public function getDisplayer(): FileDisplayerInterface
    {
        return $this->_displayer;
    }

    /**
     * File displayer setter
     * 
     * @param FileDisplayerInterface $displayer
     */
    public function setDisplayer(FileDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }
}