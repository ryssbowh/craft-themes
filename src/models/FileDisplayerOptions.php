<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use craft\base\Model;

class FileDisplayerOptions extends Model
{
    protected $_displayer;

    public function getDisplayer(): FileDisplayerInterface
    {
        return $this->_displayer;
    }

    public function setDisplayer(FileDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }
}