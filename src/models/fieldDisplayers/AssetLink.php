<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetLinkOptions;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use craft\fields\Assets;

/**
 * Renders an asset field as link
 */
class AssetLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset_link';

    /**
     * Get the limit of assets
     * 
     * @return ?int
     */
    public function getLimit(): ?int
    {
        if ($this->field instanceof Assets) {
            return $this->field->craftField->limit;
        }
        return 1;
    }

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Link to asset');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Assets::class, UserPhoto::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return AssetLinkOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        if ($this->field instanceof UserPhoto and !empty($value)) {
            $value = [$value];
        }
        return parent::beforeRender($value);
    }
}