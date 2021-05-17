<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fields\Field;
use craft\base\Model;

class FieldDisplayerOptions extends Model
{
    protected $_displayer;

    public function getDisplayer(): FieldDisplayerInterface
    {
        return $this->_displayer;
    }

    public function setDisplayer(FieldDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }
}