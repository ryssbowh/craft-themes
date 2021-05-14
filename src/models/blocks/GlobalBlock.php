<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockGlobalOptions;
use craft\base\Model;

class GlobalBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Global';

    /**
     * @var string
     */
    public $smallDescription = 'Displays a global set';

    /**
     * @var string
     */
    public $longDescription = 'Choose a global set and a view mode to display';

    /**
     * @var string
     */
    public static $handle = 'global';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockGlobalOptions;
    }

    public function getSets(): array
    {
        $all = [];
        $sets = \Craft::$app->globals->getAllSets();
        foreach ($sets as $set) {
            $all[] = [
                'uid' => $set->uid,
                'name' => $set->name
            ];
        }
        usort($all, function ($a, $b) {
            return ($a['name'] < $b['name']) ? -1 : 1;
        });
        return $all;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['sets']);
    }
}
