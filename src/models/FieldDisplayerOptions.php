<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use craft\base\Model;

class FieldDisplayerOptions extends Model
{
    /**
     * @var FieldDisplayerInterface
     */
    protected $_displayer;

    /**
     * Field displayer getter
     * 
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): FieldDisplayerInterface
    {
        return $this->_displayer;
    }

    /**
     * Field displayer stter
     * 
     * @param FieldDisplayerInterface $displayer
     */
    public function setDisplayer(FieldDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }
}