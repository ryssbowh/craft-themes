<?php
namespace Ryssbowh\CraftThemes\twig;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\twig\tokenparsers\BlockCacheTokenParser;
use Ryssbowh\CraftThemes\twig\tokenparsers\FieldDisplayerCacheTokenParser;
use Ryssbowh\CraftThemes\twig\tokenparsers\FileDisplayerCacheTokenParser;
use Ryssbowh\CraftThemes\twig\tokenparsers\ScssTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class TwigTheme extends AbstractExtension
{
    /**
     * inheritDoc
     *
     * @deprecated since 3.0.0 use craft.themes variable instead
     */
    public function getGlobals()
    {
        $service = Themes::$plugin->registry;
        return array(
            'themesRegistry' => $service,
            'theme' => $service->getCurrent()
        );
    }

    /**
     * @inheritdoc
     */
    public function getTokenParsers(): array
    {
        return [
            new BlockCacheTokenParser(),
            new FieldDisplayerCacheTokenParser(),
            new FileDisplayerCacheTokenParser(),
            new ScssTokenParser(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTests()
    {
        //Add the twig test 'is array'
        $isArray = new TwigTest('array', function ($value) {
            return is_array($value);
        });
        //Add the twig test 'is instanceof'
        $isInstance = new TwigTest('instanceof', function ($value, $class) {
            return $value instanceof $class;
        });
        //Add the twig test 'is numeric'
        $isNumeric = new TwigTest('numeric', function ($value) {
            return is_numeric($value);
        });
        return [
            $isArray,
            $isInstance,
            $isNumeric
        ];
    }

    /**
     * inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('theme_url', [$this, 'themeUrl']),
        ];
    }

    /**
     * inheritDoc
     */
    public function themeUrl($path)
    {
        return Themes::$plugin->registry->getCurrent()->getAssetUrl($path);
    }
}