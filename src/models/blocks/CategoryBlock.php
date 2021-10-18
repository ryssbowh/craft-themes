<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockCategoryOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Category;

/**
 * Block displaying some categories
 */
class CategoryBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'category';

    /**
     * @var array
     */
    protected $_categories;

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
        return \Craft::t('themes', 'Displays some categories');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose one or several categories and a view mode to display');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptionsInterface
    {
        return new BlockCategoryOptions;
    }

    /**
     * Get category as defined in options
     * 
     * @return ?Category
     */
    public function getCategories(): array
    {
        if ($this->_categories === null) {
            $this->_categories = array_map(function ($row) {
                return [
                    'category' => Category::find()->id($row['id'])->one(),
                    'viewMode' => Themes::$plugin->viewModes->getByUid($row['viewMode'])
                ];
            }, $this->options->categories);
        }
        return $this->_categories;
    }
}
