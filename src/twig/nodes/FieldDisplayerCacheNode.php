<?php
namespace Ryssbowh\CraftThemes\twig\nodes;

use Craft;
use Ryssbowh\CraftThemes\Themes;
use Twig\Compiler;
use Twig\Node\Node;

class FieldDisplayerCacheNode extends Node
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
            ->write("\$cachePrefix = \$context['field']->id;\n");

        $this->compileDisplayer($compiler);
    }

    /**
     * Write displayer's caching code into compiler
     * 
     * @param Compiler $compiler
     */
    protected function compileDisplayer(Compiler $compiler)
    {
        $n = self::$_cacheCount++;

        $compiler
            ->write("\$displayer = \$context['displayer'];\n")
            ->write('$cacheService = ' . Themes::class . "::\$plugin->displayerCache;\n")
            ->write('$request = ' . Craft::class . "::\$app->getRequest();\n")
            ->write("\$ignoreCache{$n} = (\$request->getIsLivePreview() || \$request->getToken());\n")
            ->write("if (!\$ignoreCache{$n}) {\n")
            ->indent()
            ->write("\$cacheBody{$n} = \$cacheService->getCache(\$displayer, \$cachePrefix);\n")
            ->outdent()
            ->write("} else {\n")
            ->indent()
            ->write("\$cacheBody{$n} = null;\n")
            ->outdent()
            ->write("}\n")
            ->write("if (\$cacheBody{$n} === null) {\n")
            ->indent()
            ->write("if (!\$ignoreCache{$n}) {\n")
            ->indent()
            ->write("\$cacheService->startCaching(\$displayer);\n")
            ->outdent()
            ->write("}\n")
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$cacheBody{$n} = ob_get_clean();\n")
            ->write("if (!\$ignoreCache{$n}) {\n")
            ->indent()
            ->write("\$cacheService->stopCaching(\$displayer, \$cacheBody{$n}, \$cachePrefix);\n")
            ->outdent()
            ->write("}\n")
            ->outdent()
            ->write("}\n")
            ->write("echo \$cacheBody{$n};\n");
    }
}
