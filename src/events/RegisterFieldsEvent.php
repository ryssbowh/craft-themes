<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\DateCreated;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\ElementUrl;
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
        $this->register(CraftField::class);
        $this->register(Matrix::class);
        $this->register(MatrixField::class);
        $this->register(Table::class);
        $this->register(TableField::class);
        $this->register(Title::class);
        $this->register(Author::class);
        $this->register(File::class);
        $this->register(TagTitle::class);
        $this->register(PostDate::class);
        $this->register(DateUpdated::class);
        $this->register(DateCreated::class);
        $this->register(LastLoginDate::class);
        $this->register(UserFirstName::class);
        $this->register(UserLastName::class);
        $this->register(UserUsername::class);
        $this->register(UserPhoto::class);
        $this->register(UserEmail::class);
        $this->register(ElementUrl::class);
    }

    /**
     * Register a new field
     * 
     * @param string $fieldClass
     */
    public function register(string $fieldClass, bool $replaceIfExisting = false)
    {
        if (!$replaceIfExisting and isset($this->_fields[$fieldClass::getType()])) {
            throw FieldException::alreadyDefined($fieldClass);
        }
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