<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;

/**
 * Handles the username of user layouts
 */
class UserPhoto extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'user-photo';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return get_class($layout) == UserLayout::class;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'photo';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Photo');
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        $dependencies[] = DisplayerCacheService::DISPLAYER_CACHE_TAG . '::' . $this->id;
        return [$prefix . 'photo'];
    }
}