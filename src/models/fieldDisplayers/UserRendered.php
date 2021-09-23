<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\fields\Users;

class UserRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'user_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): string
    {
        return Users::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UserRenderedOptions::class;
    }

    /**
     * Get view modes for user layout
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::USER_HANDLE);
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->uid] = $viewMode->name;
        }
        return $viewModes;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }
}