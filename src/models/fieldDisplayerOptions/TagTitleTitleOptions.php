<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TagTitleTitleOptions extends FieldDisplayerOptions
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
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'tag' => 'p'
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['tag', 'string'],
            ['tag', 'in', 'range' => array_keys($this->definitions['tag']['options'])]
        ];
    }
}