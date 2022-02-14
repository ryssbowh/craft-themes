<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\HtmlAudioOptions;
use craft\base\Model;

/**
 * Renders an audio file
 */
class HtmlAudio extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'html-audio';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Html Audio');
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
        return ['audio'];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return HtmlAudioOptions::class;
    }
}