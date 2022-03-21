<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\DateCreated;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\ElementUrl;
use Ryssbowh\CraftThemes\models\fields\ExpiryDate;
use Ryssbowh\CraftThemes\models\fields\File;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\models\fields\Missing;
use Ryssbowh\CraftThemes\models\fields\PostDate;
use Ryssbowh\CraftThemes\models\fields\Table;
use Ryssbowh\CraftThemes\models\fields\TableField;
use Ryssbowh\CraftThemes\models\fields\TagTitle;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\models\fields\UserEmail;
use Ryssbowh\CraftThemes\models\fields\UserFirstName;
use Ryssbowh\CraftThemes\models\fields\UserLastLoginDate;
use Ryssbowh\CraftThemes\models\fields\UserLastName;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use Ryssbowh\CraftThemes\models\fields\UserUsername;
use yii\base\Event;

class RegisterFieldsEvent extends Event
{
    /**
     * @var string[]
     */
    protected $_fields = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->register(CraftField::class);
        $this->register(Missing::class);
        $this->register(Matrix::class);
        $this->register(Table::class);
        $this->register(TableField::class);
        $this->register(Title::class);
        $this->register(Author::class);
        $this->register(File::class);
        $this->register(TagTitle::class);
        $this->register(PostDate::class);
        $this->register(DateUpdated::class);
        $this->register(DateCreated::class);
        $this->register(UserLastLoginDate::class);
        $this->register(UserFirstName::class);
        $this->register(UserLastName::class);
        $this->register(UserUsername::class);
        $this->register(UserPhoto::class);
        $this->register(UserEmail::class);
        $this->register(ElementUrl::class);
        $this->register(ExpiryDate::class);
    }

    /**
     * Register a new field
     *
     * @param string $fieldClass
     * @param bool $replaceIfExisting
     * @throws FieldException
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
     * Register many fields classes
     * 
     * @param array $fields
     * @param bool  $replaceIfExisting
     * @since 3.1.0
     */
    public function registerMany(array $fields, bool $replaceIfExisting = false)
    {
        foreach ($fields as $field) {
            $this->register($field, $replaceIfExisting);
        }
    }

    /**
     * Get all fields
     * 
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->_fields;
    }
}