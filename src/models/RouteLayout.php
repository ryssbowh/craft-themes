<?php

namespace Ryssbowh\CraftThemes\models;

use craft\models\CategoryGroup;

class RouteLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'route';

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Route : {route}', ['route' => $this->element]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'route_' . md5($this->getElement());
    }
}