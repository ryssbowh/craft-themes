<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\AssetBlockOptions;
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
    public function getOptionsModel(): string
    {
        return AssetBlockOptions::class;
    }

    /**
     * Get asset as defined in options
     * 
     * @return array
     */
    public function getAssets(): array
    {
        if ($this->_assets === null) {
            $this->_assets = [];
            foreach ($this->options->assets as $array) {
                try {
                    $viewMode = Themes::$plugin->viewModes->getByUid($array['viewMode']);
                } catch (ViewModeException $e) {
                    continue;
                }
                $asset = Asset::find()->id($array['id'])->one();
                if ($asset) {
                    $this->_assets[] = [
                        'asset' => $asset,
                        'viewMode' => $viewMode
                    ];
                }
            }
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
