<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockUserOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $viewMode;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['user', 'viewMode'], 'required'],
            [['user', 'viewMode'], 'string']
        ]);
    }

    /**
     * Saving the user option field after save as it's not included in project config
     * 
     * @param  BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['user'] = $this->user;
        $record->options = $options;
        $record->save(false);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [ 
            'viewMode' => $this->viewMode
        ]);
    }
}
