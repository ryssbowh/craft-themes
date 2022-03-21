<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Options for the current user block
 */
class CurrentUserBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'viewMode' => [
                'field' => 'fetchviewmode',
                'required' => true,
                'layoutType' => 'user',
                'label' => \Craft::t('themes', 'View mode')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'viewMode' => null
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['viewMode'], 'required'],
            [['viewMode'], 'string']
        ]);
    }
}
