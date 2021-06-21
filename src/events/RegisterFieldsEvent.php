<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\File;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\models\fields\MatrixField;
use Ryssbowh\CraftThemes\models\fields\Table;
use Ryssbowh\CraftThemes\models\fields\TableField;
use Ryssbowh\CraftThemes\models\fields\Title;
use yii\base\Event;

class RegisterFieldsEvent extends Event
{
    /**
     * @var array
     */
    protected $_fields = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->add(CraftField::class);
        $this->add(Matrix::class);
        $this->add(MatrixField::class);
        $this->add(Table::class);
        $this->add(TableField::class);
        $this->add(Title::class);
        $this->add(Author::class);
        $this->add(File::class);
    }

    /**
     * Register a new field
     * 
     * @param string $fieldClass
     */
    public function add(string $fieldClass)
    {
        $this->_fields[$fieldClass::getType()] = $fieldClass;
        return $this;
    }

    /**
     * Get all fields
     * 
     * @return array
     */
    public function getFields(): array
    {
        return $this->_fields;
    }
}