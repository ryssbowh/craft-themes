<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockCategoryOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Category;

class CategoryBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'category';

    /**
     * @var Category
     */
    protected $_category;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Category');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays a category');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose a category and a view mode to display');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
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
     * Get category as defined in options
     * 
     * @return Category
     */
    public function getCategory(): Category
    {
        if ($this->_category === null) {
            $this->_category = Category::find()->uid($this->options->category)->one();
        }
        return $this->_category;
    }

    /**
     * Get layout associated to category defined in options
     * 
     * @return LayoutInterface
     */
    public function getCategoryLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->layout->theme, LayoutService::CATEGORY_HANDLE, $this->options->group);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['groups']);
    }
}
