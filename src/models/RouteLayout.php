<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\CategoryGroup;

class RouteLayout extends Layout
{
    public $type = 'route';

    protected function loadElement(): ?CategoryGroup
    {
        $routes = \Craft::$app->routes->getProjectConfigRoutes();
        foreach ($routes as $index => $route) {
            if (md5($index) == $this->element) {
                $this->_element = $index;
                break;
            }
        }
        return $this->_element;
    }

    public function getDescription(): string
    {
        return \Craft::t('themes', 'Route : {route}', ['route' => $this->element]);
    }
}