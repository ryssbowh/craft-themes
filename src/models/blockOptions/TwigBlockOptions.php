<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the twig block
 */
class TwigBlockOptions extends BlockOptions
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
                'label' => \Craft::t('themes', 'Twig Code')
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
