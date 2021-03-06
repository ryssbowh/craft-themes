<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use craft\services\Routes;

class RouteLayout extends Layout
{
    /**
     * @var string
     */
    public $type = 'route';

    /**
     * @var boolean
     */
    protected $_hasFields = false;

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->projectConfig->get(Routes::CONFIG_ROUTES_KEY)[$this->element];
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Route : {route}', ['route' => $this->element()['uriPattern']]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'route_' . $this->element;
    }
}