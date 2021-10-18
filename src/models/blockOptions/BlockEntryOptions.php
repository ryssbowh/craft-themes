<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block entry
 */
class BlockEntryOptions extends BlockOptions
{
    /**
     * @var array
     */
    public $entries = [];

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['entries'], 'required'],
            ['entries', function () {
                if (!is_array($this->entries)) {
                    $this->addError('entries', \Craft::t('themes', 'Invalid entries'));
                }
            }]
        ]);
    }

    /**
     * Saving the entry option field after save as it's not included in project config
     * 
     * @param BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['entries'] = $this->entries;
        $record->options = $options;
        $record->save(false);
    }
}
