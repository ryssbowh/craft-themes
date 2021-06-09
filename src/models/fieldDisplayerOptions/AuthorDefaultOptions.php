<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AuthorDefaultOptions extends FieldDisplayerOptions
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
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['firstName', 'lastName', 'email'], 'boolean']
        ];
    }
}