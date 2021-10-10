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
     * @var boolean
     */
    public static $isDefault = true;

    /**
     * @var string
     */
    public static $handle = 'html_video';

    /**
     * @var boolean
     */
    public $hasOptions = true;

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
    public static function getKindTargets()
    {
        return ['video'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new HtmlVideoOptions;
    }
}