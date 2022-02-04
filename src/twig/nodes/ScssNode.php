<?php
namespace Ryssbowh\CraftThemes\twig\nodes;

use Craft;
use Ryssbowh\CraftThemes\Themes;
use Twig\Compiler;
use Twig\Node\Node;
use craft\web\AssetBundle;

class ScssNode extends Node
{
    /**
     * @var int
     */
    private static $_scssCount = 1;

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $n = self::$_scssCount++;

        $options = $this->hasNode('options') ? $this->getNode('options') : null;
        $file = $this->hasNode('file') ? $this->getNode('file')->getAttribute('value') : null;
        $force = $this->getAttribute('force');
        $scss = $this->hasNode('options') ? $this->getNode('body')->getAttribute('data') : null;
        $template = $this->getSourceContext()->getPath();
        $hash = hash('crc32b', $template . '-' . $n);
        $compiler->addDebugInfo($this);
        if ($options) {
            $compiler->write("\$options = ")
            ->subcompile($options)
            ->write(";\n");
        } else {
            $compiler->write("\$options = [];\n");
        }
        $compiler->write("\$force = " . ($force ? 'true' : 'false') . ";\n")
            ->write("\$template = '$template';\n")
            ->write("\$scssService = " . Themes::class . "::\$plugin->scss;\n")
            ->write("\$theme = " . Themes::class . "::\$plugin->registry->current;\n");
        if ($file) {
            $compiler->write("\$scssService->compileInlineFile('$file', '$hash', \$theme, \$template, \$options, \$force);\n");
        } else {
            $compiler->write("\$scss = <<<EOF\n")
            ->write($scss . "\n")
            ->outdent()->outdent()
            ->write("EOF;\n")
            ->indent()->indent()
            ->write("\$scssService->compileInlineScss(\$scss, '$hash', \$theme, \$template, \$options, \$force);\n");
        }
    }
}
