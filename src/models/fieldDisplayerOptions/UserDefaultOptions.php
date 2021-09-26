<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class UserDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $firstName = true;

    /**
     * @var boolean
     */
    public $lastName = true;

    /**
     * @var boolean
     */
    public $email = false;

    /**
     * @var boolean
     */
    public $linkEmail = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['firstName', 'lastName', 'email', 'linkEmail'], 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}