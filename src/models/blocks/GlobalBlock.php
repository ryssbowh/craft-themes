<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockGlobalOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\GlobalSet;

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
     * @var GlobalSet
     */
    protected $_global;

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockGlobalOptions;
    }

    /**
     * Get all global sets as array
     * 
     * @return array
     */
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

    /**
     * Get global set as defined in options
     * 
     * @return GlobalSet
     */
    public function getGlobalSet(): GlobalSet
    {
        if ($this->_global === null) {
            $this->_global = GlobalSet::find()->uid($this->options->set)->one();
        }
        return $this->_global;
    }

    /**
     * Get layout associated to global set defined in options
     * 
     * @return LayoutInterface
     */
    public function getGlobalSetLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->layout->theme, LayoutService::GLOBAL_HANDLE, $this->options->set);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['sets']);
    }
}
