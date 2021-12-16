<?php
namespace $NAMESPACE;

use $OPTIONSNAMESPACE\$OPTIONSCLASS;
use Ryssbowh\CraftThemes\models\FieldDisplayer;

class $CLASSNAME extends FieldDisplayer
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
    public static function getFieldTargets(): array
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
