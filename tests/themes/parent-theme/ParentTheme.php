<?php 

namespace Ryssbowh\tests\themes\parent;

use Ryssbowh\CraftThemes\models\ThemePlugin;
use yii\base\Event;

class ParentTheme extends ThemePlugin
{
    public function getExtends(): string
    {
        return 'partial-theme';
    }

    /**
     * @inheritDoc
     */
    protected function defineRegions(): ?array
    {
        return [
            [
                'handle' => 'header-left',
                'name' => \Craft::t('themes', 'Header Left'),
                'width' => '49%',
            ],
            [
                'handle' => 'header-right',
                'name' => \Craft::t('themes', 'Header Right'),
                'width' => '49%',
            ],
            [
                'handle' => 'banner',
                'name' => \Craft::t('themes', 'Banner'),
                'width' => '100%',
            ],
            [
                'handle' => 'before-content',
                'name' => \Craft::t('themes', 'Before Content'),
                'width' => '100%',
            ],
            [
                'handle' => 'content',
                'name' => \Craft::t('themes', 'Content'),
                'width' => '100%',
            ],
            [
                'handle' => 'after-content',
                'name' => \Craft::t('themes', 'After Content'),
                'width' => '100%',
            ],
            [
                'handle' => 'footer-left',
                'name' => \Craft::t('themes', 'Footer Left'),
                'width' => '49%',
            ],
            [
                'handle' => 'footer-right',
                'name' => \Craft::t('themes', 'Footer Right'),
                'width' => '49%',
            ]
        ];
    }
}