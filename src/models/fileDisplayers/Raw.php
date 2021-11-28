<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\RawOptions;
use craft\base\Model;

/**
 * Renders a file as raw content
 */
class Raw extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'raw';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Raw');
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets()
    {
        return ['javascript', 'html', 'php', 'text', 'xml', 'json'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return RawOptions::class;
    }
}