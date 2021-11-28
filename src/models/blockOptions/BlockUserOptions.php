<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block user
 */
class BlockUserOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'users' => [
                'field' => 'elements',
                'required' => true,
                'elementType' => 'users',
                'addElementLabel' => \Craft::t('app', 'Add a user'),
                'label' => \Craft::t('app', 'Users'),
                'saveInConfig' => false
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'users' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['users'], 'required'],
            ['users', function () {
                if (!is_array($this->users)) {
                    $this->addError('users', \Craft::t('themes', 'Invalid users'));
                }
            }]
        ]);
    }
}
