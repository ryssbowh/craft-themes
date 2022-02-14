<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;

/**
 * The field Author is added to all channels and structures
 */
class Author extends Field
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'author';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        if ($layout instanceof EntryLayout) {
            return $layout->element->section->type != 'single';
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'author';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Author');
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        if (!$this->displayer) {
            return [];
        }
        if ($level >= Themes::$plugin->settings->maxEagerLoadLevel) {
            \Craft::info("Maximum eager loaging level (" . Themes::$plugin->settings->maxEagerLoadLevel . ') reached', __METHOD__);
            return [];
        }
        $dependencies[] = DisplayerCacheService::DISPLAYER_CACHE_TAG . '::' . $this->id;
        $with = $prefix . 'author';
        return $this->displayer->eagerLoad([$with], $with . '.', $level + 1);
    }
}