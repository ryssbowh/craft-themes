<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockAssetOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Asset;

/**
 * Block displaying some assets
 */
class AssetBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'asset';

    /**
     * @var array
     */
    protected $_assets;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Asset');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays some assets');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose one or several assets and a view mode to display');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptionsInterface
    {
        return new BlockAssetOptions;
    }

    /**
     * Get asset as defined in options
     * 
     * @return array
     */
    public function getAssets(): array
    {
        if ($this->_assets === null) {
            $this->_assets = array_map(function ($row) {
                return [
                    'asset' => Asset::find()->id($row['id'])->one(),
                    'viewMode' => Themes::$plugin->viewModes->getByUid($row['viewMode'])
                ];
            }, $this->options->assets);
        }
        return $this->_assets;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(bool $fromCache): bool
    {
        return sizeof($this->assets) > 0;
    }
}
