<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the category block
 */
class CategoryBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'categories' => [
                'field' => 'elements',
                'elementType' => 'categories',
                'addElementLabel' => \Craft::t('app', 'Add a category'),
                'required' => true,
                'label' => \Craft::t('app', 'Categories'),
                'saveInConfig' => false
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'categories' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['categories'], 'required'],
            ['categories', function () {
                if (!is_array($this->categories)) {
                    $this->addError('categories', \Craft::t('themes', 'Invalid categories'));
                }
            }]
        ]);
    }
}
