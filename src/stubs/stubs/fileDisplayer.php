<?php
namespace $NAMESPACE;

use $OPTIONSNAMESPACE\$OPTIONSCLASS;
use Ryssbowh\CraftThemes\models\FileDisplayer;

class $CLASSNAME extends FileDisplayer
{
    /**
     * @inheritDoc
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
    public static function getKindTargets(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return $OPTIONSCLASS::class;
    }
}
