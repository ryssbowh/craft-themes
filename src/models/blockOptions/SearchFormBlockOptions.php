<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the search form block
 */
class SearchFormBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'action' => [
                'field' => 'text',
                'required' => true,
                'label' => \Craft::t('themes', 'Form action')
            ],
            'inputName' => [
                'field' => 'text',
                'required' => true,
                'label' => \Craft::t('themes', 'Search term name')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'action' => 'search',
            'inputName' => 'term'
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['action', 'inputName'], 'string'],
            [['action', 'inputName'], 'required']
        ]);
    }
}
