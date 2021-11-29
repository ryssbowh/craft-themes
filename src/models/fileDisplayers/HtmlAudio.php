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
     * @var boolean
     */
    public static $isDefault = true;

    /**
     * @var string
     */
    public static $handle = 'html_audio';

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
    public static function getKindTargets()
    {
        return ['audio'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return HtmlAudioOptions::class;
    }
}