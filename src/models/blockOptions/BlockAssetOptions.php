<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block asset
 */
class BlockAssetOptions extends BlockOptions
{
    /**
     * @var array
     */
    public $assets = [];

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

    /**
     * Saving the asset option field after save as it's not included in project config
     * 
     * @param BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['assets'] = $this->assets;
        $record->options = $options;
        $record->save(false);
    }
}
