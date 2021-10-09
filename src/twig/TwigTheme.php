<?php

namespace Ryssbowh\CraftThemes\twig;

use Ryssbowh\CraftThemes\Themes;
use Twig\Extension\AbstractExtension;

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