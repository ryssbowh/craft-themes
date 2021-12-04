<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\LinkOptions;
use craft\base\Model;

/**
 * Renders a file as link
 */
class Link extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'link';

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
    public static function isDefault(string $kind): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets()
    {
        return '*';
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return LinkOptions::class;
    }
}