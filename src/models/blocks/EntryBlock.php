<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockEntryOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

/**
 * Block displaying some entries
 */
class EntryBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'entry';

    /**
     * @var array
     */
    protected $_entries;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Entry');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays some entries');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose one or several entries and a view mode to display');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return BlockEntryOptions::class;
    }

    /**
     * Get entries/view modes as defined in options
     * 
     * @return array
     */
    public function getEntries(): array
    {
        if ($this->_entries === null) {
            $this->_entries = array_map(function ($row) {
                return [
                    'entry' => Entry::find()->id($row['id'])->one(),
                    'viewMode' => Themes::$plugin->viewModes->getByUid($row['viewMode'])
                ];
            }, $this->options->entries);
        }
        return $this->_entries;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(bool $fromCache): bool
    {
        return sizeof($this->entries) > 0;
    }
}
