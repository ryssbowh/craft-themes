<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block category
 */
class BlockCategoryOptions extends BlockOptions
{
    /**
     * @var array
     */
    public $categories = [];

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

    /**
     * Saving the categories option field after save as it's not included in project config
     * 
     * @param BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['categories'] = $this->categories;
        $record->options = $options;
        $record->save(false);
    }
}
