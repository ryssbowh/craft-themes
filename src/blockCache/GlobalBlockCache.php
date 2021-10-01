<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

class GlobalBlockCache extends BlockCacheStrategy
{
    const CACHE_TAG = 'themes.blockCache.global';

    /**
     * @var MobileDetect
     */
    protected $mobileDetect;

    public function init()
    {
        parent::init();
        $this->mobileDetect = new MobileDetect;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'global';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Global');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Block will be cached globally');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockCacheStrategyOptions
    {
        return new GlobalOptions;
    }

    /**
     * @inheritDoc
     */
    protected function getKey(BlockInterface $block): string
    {
        $key = [];
        if ($this->options->cachePerAuthenticated) {
            $key[] = \Craft::$app->user ? 'auth' : 'noauth';
        }
        if ($this->options->cachePerViewport) {
            $key[] = $this->getViewPort();
        }
        if ($this->options->cachePerUser and $user = \Craft::$app->user) {
            $key[] = $user->getIdentity()->id;
        }
        return implode('-', $key);
    }

    /**
     * @inheritDoc
     */
    protected function getKeyPrefix(): string
    {
        return self::CACHE_TAG;
    }

    /**
     * @inheritDoc
     */
    protected function getTag(): string
    {
        return self::CACHE_TAG;
    }

    /**
     * Get user's view port
     * 
     * @return string
     */
    protected function getViewPort(): string
    {
        if ($this->mobileDetect->isMobile()) {
            return 'phone';
        }
        if ($this->mobileDetect->isTablet()) {
            return 'tablet';
        }
        return 'desktop';
    }
}