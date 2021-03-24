<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpRepairController extends Controller
{
    public function actionTest()
    {
        $this->layouts->createAll();
        $this->display->createAll();
    }
}