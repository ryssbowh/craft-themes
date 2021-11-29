<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the user block
 */
class UserBlockOptions extends BlockOptions
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
                foreach ($this->users as $array) {
                    try {
                        Themes::$plugin->viewModes->getByUid($array['viewMode']);
                    } catch (ViewModeException $e) {
                        $this->addError('users', [$array['id'] => \Craft::t('themes', 'View mode is invalid')]);
                    }
                }
            }]
        ]);
    }
}
