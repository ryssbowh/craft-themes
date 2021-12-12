<?php
namespace Ryssbowh\CraftThemes\twig\nodes;

use Twig\Compiler;

class FileDisplayerCacheNode extends FieldDisplayerCacheNode
{
    /**
     * @var int
     */
    private static $_cacheCount = 1;

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("\$cachePrefix = \$context['field']->id . '-' . \$context['asset']->id;\n");

        $this->compileDisplayer($compiler);
    }
}
