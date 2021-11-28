<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block twig
 */
class BlockTwigOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'twig' => [
                'field' => 'textarea',
                'required' => true,
                'label' => \Craft::t('app', 'Twig Code')
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'twig' => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['twig', 'string']
        ]);
    }
}
