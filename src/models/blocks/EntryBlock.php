<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockEntryOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

class EntryBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Entry';

    /**
     * @var string
     */
    public $smallDescription = 'Displays an entry';

    /**
     * @var string
     */
    public $longDescription = 'Choose an entry and a view mode to display';

    /**
     * @var string
     */
    public static $handle = 'entry';

    /**
     * @var Entry
     */
    protected $_entry;

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockEntryOptions;
    }

    /**
     * Get all entry types as array
     * 
     * @return array
     */
    public function getEntryTypes(): array
    {
        $entryTypes = [];
        $types = \Craft::$app->sections->getAllEntryTypes();
        foreach ($types as $type) {
            $entryTypes[] = [
                'uid' => $type->uid,
                'name' => $type->name
            ];
        }
        usort($entryTypes, function ($a, $b) {
            return ($a['name'] < $b['name']) ? -1 : 1;
        });
        return $entryTypes;
    }

    /**
     * Get entry as defined in options
     * 
     * @return Entry
     */
    public function getEntry(): Entry
    {
        if ($this->_entry === null) {
            $this->_entry = Entry::find()->uid($this->options->entry)->one();
        }
        return $this->_entry;
    }

    /**
     * Get layout associated to entry defined in options
     * 
     * @return LayoutInterface
     */
    public function getEntryLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->layout->theme, LayoutService::ENTRY_HANDLE, $this->options->type);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['entryTypes']);
    }
}
