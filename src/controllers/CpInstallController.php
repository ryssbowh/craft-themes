<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpInstallController extends Controller
{
    public function actionTest()
    {
        $this->layouts->install();
        $this->display->install();
    }
}