<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;
use craft\elements\Category;

/**
 * Options for the block asset
 */
class BlockAssetOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'assets' => [
                'field' => 'elements',
                'elementType' => 'assets',
                'addElementLabel' => \Craft::t('app', 'Add an asset'),
                'required' => true,
                'label' => \Craft::t('app', 'Assets'),
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
            'assets' => []
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['assets'], 'required'],
            ['assets', function () {
                if (!is_array($this->assets)) {
                    $this->addError('assets', \Craft::t('themes', 'Invalid assets'));
                }
            }]
        ]);
    }
}
