<?php
namespace $NAMESPACE;

use Ryssbowh\CraftThemes\base\ThemePlugin;
use Ryssbowh\CraftThemes\events\RegisterBlockProviders;
use Ryssbowh\CraftThemes\events\RegisterFieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\RegisterFileDisplayerEvent;
use Ryssbowh\CraftThemes\services\BlockProvidersService;
use Ryssbowh\CraftThemes\services\FieldDisplayerService;
use Ryssbowh\CraftThemes\services\FileDisplayerService;
use yii\base\Event;

class $MAINCLASS extends ThemePlugin
{
    /**
     * @var $MAINCLASS
     */
    public static $plugin;

    /**
     * @inheritDoc
     */
    protected $assetBundles = [
        '*' => [
        ]
    ];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this::$plugin = $this;
        $this->registerFieldDisplayers();
        $this->registerFileDisplayers();
        $this->registerBlockProviders();
    }

    /**
     * @inheritDoc
     */
    public function afterThemeInstall()
    {
        if ($this->hasDataInstalled()) {
            return;
        }
    }

    /**
     * Register new block providers
     */
    protected function registerBlockProviders()
    {
        Event::on(BlockProvidersService::class, BlockProvidersService::EVENT_REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProviders $event) {

        });
    }

    /**
     * Register new field displayers
     */
    protected function registerFieldDisplayers()
    {
        Event::on(FieldDisplayerService::class, FieldDisplayerService::EVENT_REGISTER_DISPLAYERS, function (RegisterFieldDisplayerEvent $event) {

        });
    }

    /**
     * Register new file displayers
     */
    protected function registerFileDisplayers()
    {
        Event::on(FileDisplayerService::class, FileDisplayerService::EVENT_REGISTER_DISPLAYERS, function (RegisterFileDisplayerEvent $event) {

        });
    }

    /**
     * @inheritDoc
     */
    protected function defineRegions(): ?array
    {
        return [
            [
                'handle' => 'header',
                'name' => \Craft::t('$HANDLE', 'Header'),
                'width' => '100%',
            ],
            [
                'handle' => 'content',
                'name' => \Craft::t('$HANDLE', 'Content'),
                'width' => '100%',
            ],
            [
                'handle' => 'footer',
                'name' => \Craft::t('$HANDLE', 'Footer'),
                'width' => '100%',
            ]
        ];
    }
}