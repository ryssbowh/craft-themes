<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block user
 */
class BlockUserOptions extends BlockOptions
{
    /**
     * @var array
     */
    public $users = [];

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['users'], 'required'],
            ['users', function () {
                if (!is_array($this->users)) {
                    $this->addError('users', \Craft::t('themes', 'Invalid users'));
                }
            }]
        ]);
    }

    /**
     * Saving the user option field after save as it's not included in project config
     * 
     * @param BlockInterface $block
     */
    public function afterSave(BlockInterface $block)
    {
        $record = Themes::$plugin->blocks->getRecordByUid($block->uid);
        $options = json_decode($record->options, true);
        $options['users'] = $this->users;
        $record->options = $options;
        $record->save(false);
    }
}
