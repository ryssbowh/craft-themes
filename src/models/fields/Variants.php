<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;

/**
 * Handles the stock of variants
 */
class Variants extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'variants';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return $layout instanceof ProductLayout;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'variants';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Variants');
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
        $with = $prefix . 'variants';
        return $this->displayer->eagerLoad([$with], $with . '.', $level + 1);
    }
}