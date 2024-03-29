<?php
namespace Ryssbowh\CraftThemes\blockCache;

use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\base\BlockCacheStrategy;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

/**
 * This strategy will cache blocks regardless of the url.
 * It has options to cache differently for each user, for guests/non guests or for each view port (mobile, tablet, desktop)
 */
class GlobalBlockCache extends BlockCacheStrategy
{
    const CACHE_TAG = 'themes.blockCache.global';

    /**
     * @var MobileDetect
     */
    protected $mobileDetect;

    /**
     * @inheritDoc
     */
    public function init(): void
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
    public function getDuration(): ?int
    {
        return $this->options->duration * 60;
    }

    /**
     * @inheritDoc
     */
    public function buildKey(BlockInterface $block): array
    {
        $key = [self::CACHE_TAG];
        $identity = \Craft::$app->user->identity;
        if ($this->options->cachePerAuthenticated) {
            $key[] = $identity ? 'auth' : 'noauth';
        }
        if ($this->options->cachePerViewport) {
            $key[] = 'view-port-' . $this->getViewPort();
        }
        if ($this->options->cachePerUser and $identity) {
            $key[] = 'user-id-' . $identity->id;
        }
        if ($this->options->cachePerSite) {
            $site = \Craft::$app->sites->getCurrentSite();
            $key[] = 'site-' . $site->id;
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