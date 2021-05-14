<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockCategoryOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $group;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $viewMode;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['group', 'category', 'viewMode'], 'required'],
            [['group', 'category', 'viewMode'], 'string']
        ]);
    }

    /**
     * Saving the category option field after save as it's not included in project config
     * 
     * @param  BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['category'] = $this->category;
        $record->options = $options;
        $record->save(false);
    }

    public function getConfig(): array
    {
        return [
            'group' => $this->group, 
            'viewMode' => $this->viewMode
        ];
    }
}
