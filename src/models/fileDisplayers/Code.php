<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;

class Code extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'code';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Code');
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets()
    {
        return ['javascript', 'html', 'php', 'text', 'xml'];
    }
}