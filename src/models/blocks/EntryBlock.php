<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\EntryBlockOptions;
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
        return EntryBlockOptions::class;
    }

    /**
     * Get entries/view modes as defined in options
     * 
     * @return array
     */
    public function getEntries(): array
    {
        if ($this->_entries === null) {
            $this->_entries = [];
            foreach ($this->options->entries as $array) {
                try {
                    $viewMode = Themes::$plugin->viewModes->getByUid($array['viewMode']);
                } catch (ViewModeException $e) {
                    continue;
                }
                $entry = Entry::find()->id($array['id'])->one();
                if ($entry) {
                    $this->_entries[] = [
                        'entry' => $entry,
                        'viewMode' => $viewMode
                    ];
                }
            }
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
