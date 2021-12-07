<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\ContentBlockOptions;

/**
 * Special block to handle the content of the page. 
 * Its content will be the current page layout's displays
 */
class ContentBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'content';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Content');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the main page content');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Should be present on each block layout');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return ContentBlockOptions::class;
    }

    /**
     * Calls each field displayer beforeRender() in case the block is cached
     * to make sure every field displayer gets a chance of initializing themselves
     * 
     * @param  bool $fromCache
     * @return bool
     */
    public function beforeRender(bool $fromCache): bool
    {
        if (!$fromCache) {
            return true;
        }
        $viewMode = Themes::$plugin->view->renderingViewMode;
        foreach ($viewMode->visibleDisplays as $display) {
            try {
                $displayer = $display->item->displayer;
            } catch (\Exception $e) {
            }
            if ($displayer) {
                $value = $display->item->renderingValue;
                $displayer->beforeRender($value);
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCacheTags(): array
    {
        $element = Themes::$plugin->view->renderingElement;
        $tags = $element->getCacheTags();
        $tags[] = 'element::' . get_class($element) . '::' . $element->id;
        return $tags;
    }
}