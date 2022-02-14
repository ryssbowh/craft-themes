<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;
use craft\fields\Users;

/**
 * Renders a user field as a slick carousel
 */
class UserSlick extends UserRendered
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'user-slick';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Slick Carousel');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Users::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return UserSlickOptions::class;
    }
}