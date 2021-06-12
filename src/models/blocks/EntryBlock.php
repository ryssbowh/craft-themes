<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockEntryOptions;

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
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['entryTypes']);
    }
}
