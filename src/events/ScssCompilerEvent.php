<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\ScssPhp\Compiler;
use yii\base\Event;

class ScssCompilerEvent extends Event
{
    /**
     * @var Compiler
     */
    public $compiler;
}