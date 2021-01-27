<?php

namespace Ryssbowh\CraftThemes\twig;

use Ryssbowh\CraftThemes\Themes;
use Twig\Extension\AbstractExtension;

class TwigTheme extends AbstractExtension
{
    /**
     * inheritDoc
     */
    public function getGlobals()
    {
        $service = Themes::$plugin->themes;
        return array(
            'themes' => $service,
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
        return Themes::$plugin->themes->getCurrent()->getAssetUrl($path);
    }
}