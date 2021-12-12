<?php
namespace Ryssbowh\CraftThemes\blockCache;

use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\base\BlockCacheStrategy;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

/**
 * This strategy will cache blocks differently for each url path, query string included.
 * It has options to cache differently for each user, for guests/non guests or for each view port (mobile, tablet, desktop)
 */
class QueryBlockCache extends BlockCacheStrategy
{
    const CACHE_TAG = 'themes.blockCache.query';

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
        return 'query';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Url path (with query)');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Block will be cached differently for each url');
    }

    /**
     * @inheritDoc
     */
    public function getDuration(): ?int
    {
        if ($this->options->duration === 0) {
            return null;
        }
        return $this->options->duration * 60;
    }

    /**
     * @inheritDoc
     */
    public function buildKey(BlockInterface $block): array
    {
        $key = [self::CACHE_TAG, \Craft::$app->request->getFullPath() . '?' . \Craft::$app->request->getQueryStringWithoutPath()];
        if ($this->options->cachePerAuthenticated) {
            $key[] = \Craft::$app->user ? 'auth' : 'noauth';
        }
        if ($this->options->cachePerViewport) {
            $key[] = $this->getViewPort();
        }
        if ($this->options->cachePerUser and $user = \Craft::$app->user) {
            $key[] = $user->getIdentity()->id;
        }
        return $key;
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

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return GlobalOptions::class;
    }
}