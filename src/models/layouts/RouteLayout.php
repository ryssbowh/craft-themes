<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\helpers\StringHelper;
use craft\services\Routes;

class RouteLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::ROUTE_HANDLE;

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
    public function getElementMachineName(): string
    {
        return md5($this->element()['uriPattern']);
    }

    /**
     * @inheritDoc
     */
    public function canHaveUrls(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->getElementMachineName() . '_' . $this->theme);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Route : {route}', ['route' => $this->element()['uriPattern']]);
    }
}