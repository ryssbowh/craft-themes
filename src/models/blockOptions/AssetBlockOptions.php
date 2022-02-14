<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the asset block
 */
class AssetBlockOptions extends BlockOptions
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
                foreach ($this->assets as $array) {
                    try {
                        Themes::$plugin->viewModes->getByUid($array['viewMode']);
                    } catch (ViewModeException $e) {
                        $this->addError('assets', [$array['id'] => \Craft::t('themes', 'View mode is invalid')]);
                    }
                }
            }]
        ]);
    }
}
