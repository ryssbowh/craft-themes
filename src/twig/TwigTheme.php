<?php

namespace Ryssbowh\CraftThemes\twig;

use Ryssbowh\CraftThemes\Themes;
use Twig\Extension\AbstractExtension;

class TwigTheme extends AbstractExtension
{
    public function getGlobals()
    {
        $service = Themes::$plugin->registry;
        return array(
            'themes' => $service,
            'theme' => $service->getCurrent()
        );
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('theme_url', [$this, 'themeUrl']),
        ];
    }

    public function themeUrl($value)
    {
        $value = '@themePath/'.trim($value, '/');
        return \Craft::$app->assetManager->getPublishedUrl($value, true);
    }
}