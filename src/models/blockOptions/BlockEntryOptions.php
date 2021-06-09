<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockEntryOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $entry;

    /**
     * @var string
     */
    public $viewMode;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['type', 'entry', 'viewMode'], 'required'],
            [['type', 'entry', 'viewMode'], 'string']
        ]);
    }

    /**
     * Saving the entry option field after save as it's not included in project config
     * 
     * @param  BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['entry'] = $this->entry;
        $record->options = $options;
        $record->save(false);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'type' => $this->type, 
            'viewMode' => $this->viewMode
        ];
    }
}
