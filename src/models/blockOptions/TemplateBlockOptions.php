<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the template block
 */
class TemplateBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'template' => [
                'field' => 'text',
                'required' => true,
                'label' => \Craft::t('themes', 'Template Path')
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'template' => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['template', 'string'],
            ['template', 'required']
        ]);
    }
}
