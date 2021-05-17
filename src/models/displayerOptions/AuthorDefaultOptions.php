<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AuthorDefaultOptions extends FieldDisplayerOptions
{
    public $firstName = true;
    public $lastName = true;
    public $email = false;

    public function defineRules(): array
    {
        return [
            [['firstName', 'lastName', 'email'], 'boolean']
        ];
    }
}