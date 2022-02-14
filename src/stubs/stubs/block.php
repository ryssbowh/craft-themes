<?php
namespace $NAMESPACE;

use Ryssbowh\CraftThemes\models\Block;
use $OPTIONSNAMESPACE\$OPTIONSCLASS;

class $CLASSNAME extends Block
{
    /**
     * @var string
     */
    public static $handle = '$HANDLE';

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
    public function getSmallDescription(): string
    {
        return \Craft::t('$THEMEHANDLE', '$DESCRIPTION');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return $OPTIONSCLASS::class;
    }
}
