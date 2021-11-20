<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UrlLinkOptions;
use craft\base\Model;
use craft\fields\Url;

/**
 * Renders a url field
 */
class UrlLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'url_link';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Link');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Url::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UrlLinkOptions::class;
    }
}