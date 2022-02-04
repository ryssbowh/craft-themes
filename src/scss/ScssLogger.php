<?php
namespace Ryssbowh\CraftThemes\scss;

use ScssPhp\ScssPhp\Logger\LoggerInterface;

class ScssLogger implements LoggerInterface
{
    const CATEGORY = 'scss-compilation';

    /**
     * @inheritDoc
     */
    public function warn($message, $deprecation = false)
    {
        \Craft::warning(($deprecation ? '(deprecation) ' : '') . $message, self::CATEGORY);
    }

    /**
     * @inheritDoc
     */
    public function debug($message)
    {
        \Craft::debug($message, self::CATEGORY);
    }
}