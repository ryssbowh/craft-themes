<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\HtmlVideoOptions;
use craft\base\Model;

/**
 * Renders a video file
 */
class HtmlVideo extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'html_video';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Html Video');
    }

    /**
     * @inheritDoc
     */
    public static function isDefault(string $kind): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets(): array
    {
        return ['video'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return HtmlVideoOptions::class;
    }
}