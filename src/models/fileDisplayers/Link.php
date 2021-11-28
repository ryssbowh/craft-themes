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
     * @var boolean
     */
    public static $isDefault = true;

    /**
     * @var string
     */
    public static $handle = 'link';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

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