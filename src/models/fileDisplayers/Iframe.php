<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\IframeOptions;
use craft\base\Model;

/**
 * Renders a file as iframe
 */
class Iframe extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'iframe';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Iframe');
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets(): array
    {
        return ['html'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return IframeOptions::class;
    }
}