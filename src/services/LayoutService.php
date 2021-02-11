<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\records\BlockRecord;

class LayoutService extends Service
{
    public function getLayout(ThemeInterface $theme): Layout
    {
        return (new Layout([
            'theme' => $theme
        ]))->loadFromDb();
    }

    public function getPageLayout(ThemeInterface $theme): PageLayout
    {
        return (new PageLayout([
            'theme' => $theme
        ]))->loadFromDb();
    }    
}