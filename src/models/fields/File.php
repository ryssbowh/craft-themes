<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;

class File extends Field
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'file';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return ($layout instanceof VolumeLayout);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'file';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'File');
    }
}