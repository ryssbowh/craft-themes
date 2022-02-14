<?php
namespace $NAMESPACE;

use Ryssbowh\CraftThemes\base\BlockProvider;

class $CLASSNAME extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('$THEMEHANDLE', '$NAME');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return '$HANDLE';
    }
}
