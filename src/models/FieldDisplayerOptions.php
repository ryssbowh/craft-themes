<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\models\fields\Field;
use craft\base\Model;

class FieldDisplayerOptions extends Model
{
    protected $_field;

    public function getField(): Field
    {
        return $this->_field;
    }

    public function setField(Field $field)
    {
        $this->_field = $field;
    }
}