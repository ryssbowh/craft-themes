<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockCategoryOptions;
use craft\base\Model;

class CategoryBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Category';

    /**
     * @var string
     */
    public $smallDescription = 'Displays a category';

    /**
     * @var string
     */
    public $longDescription = 'Choose a category and a view mode to display';

    /**
     * @var string
     */
    public static $handle = 'category';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockCategoryOptions;
    }

    /**
     * Get all category groups as array
     * 
     * @return array
     */
    public function getGroups(): array
    {
        $groups = [];
        $cats = \Craft::$app->categories->getAllGroups();
        foreach ($cats as $cat) {
            $groups[] = [
                'uid' => $cat->uid,
                'name' => $cat->name
            ];
        }
        usort($groups, function ($a, $b) {
            return ($a['name'] < $b['name']) ? -1 : 1;
        });
        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['groups']);
    }
}
