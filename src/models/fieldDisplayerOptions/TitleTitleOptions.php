<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TitleTitleOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'tag' => [
                'field' => 'select',
                'options' => [
                    'h1' => 'h1', 
                    'h2' => 'h2', 
                    'h3' => 'h3', 
                    'h4' => 'h4', 
                    'h5' => 'h5', 
                    'h6' => 'h6', 
                    'p' => 'p',
                    'span' => 'span'
                ],
                'required' => true,
                'label' => \Craft::t('themes', 'Html tag')
            ],
            'linked' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Link to Element')
            ],
            'newTab' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Open in new tab')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'tag' => 'h1',
            'linked' => true,
            'newTab' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['tag', 'string'],
            ['tag', 'in', 'range' => array_keys($this->definitions['tag']['options'])],
            [['linked', 'newTab'], 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}