<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderFileOptions;
use craft\base\Model;
use craft\fields\Assets;
use craft\helpers\Assets as AssetsHelper;

/**
 * Renders the file of an asset field
 */
class AssetRenderFile extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_render_file';

    /**
     * @var array
     */
    protected $_displayerMapping;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Render file');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): string
    {
        return Assets::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return AssetRenderFileOptions::class;
    }

    /**
     * Get available displayers, indexed by asset kind
     * 
     * @return array
     */
    public function getDisplayersMapping(): array
    {
        if ($this->_displayerMapping === null) {
            $allowed = $this->field->craftField->allowedKinds;
            if (!$this->field->craftField->restrictFiles) {
                $allowed = array_keys(AssetsHelper::getFileKinds());
            }
            $mapping = [];
            foreach ($allowed as $kind) {
                $displayers = Themes::$plugin->fileDisplayers->getForKind($kind);
                foreach ($displayers as $displayer) {
                    if ($options = $this->options->getOptionsForDisplayer($kind, $displayer::$handle)) {
                        $displayer->options->setAttributes($options);
                    }
                }
                $mapping[$kind] = [
                    'label' => AssetsHelper::getFileKindLabel($kind),
                    'displayers' => $displayers
                ];
            }
            $this->_displayerMapping = $mapping;
        }
        return $this->_displayerMapping;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['displayersMapping']);
    }
}