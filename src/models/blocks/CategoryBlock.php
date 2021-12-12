<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\CategoryBlockOptions;
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
     * Get category as defined in options
     * 
     * @return ?Category
     */
    public function getCategories(): array
    {
        if ($this->_categories === null) {
            $this->_categories = [];
            foreach ($this->options->categories as $array) {
                try {
                    $viewMode = Themes::$plugin->viewModes->getByUid($array['viewMode']);
                } catch (ViewModeException $e) {
                    continue;
                }
                $category = Category::find()->id($array['id'])->one();
                if ($category) {
                    $this->_categories[] = [
                        'category' => $category,
                        'viewMode' => $viewMode
                    ];
                }
            }
        }
        return $this->_categories;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return sizeof($this->categories) > 0;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return CategoryBlockOptions::class;
    }
}
