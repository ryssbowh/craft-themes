<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\DateCreated;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\File;
use Ryssbowh\CraftThemes\models\fields\LastLoginDate;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\models\fields\MatrixField;
use Ryssbowh\CraftThemes\models\fields\PostDate;
use Ryssbowh\CraftThemes\models\fields\Table;
use Ryssbowh\CraftThemes\models\fields\TableField;
use Ryssbowh\CraftThemes\models\fields\TagTitle;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\models\fields\UserEmail;
use Ryssbowh\CraftThemes\models\fields\UserFirstName;
use Ryssbowh\CraftThemes\models\fields\UserLastName;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use Ryssbowh\CraftThemes\models\fields\UserUsername;
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
        $this->add(TagTitle::class);
        $this->add(PostDate::class);
        $this->add(DateUpdated::class);
        $this->add(DateCreated::class);
        $this->add(LastLoginDate::class);
        $this->add(UserFirstName::class);
        $this->add(UserLastName::class);
        $this->add(UserUsername::class);
        $this->add(UserPhoto::class);
        $this->add(UserEmail::class);
    }

    /**
     * Register a new field, will replace fields with same type
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