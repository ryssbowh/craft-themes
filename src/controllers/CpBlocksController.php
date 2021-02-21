<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpBlocksController extends Controller
{
    public function actionProviders()
    {
        $this->requireAcceptsJson();
        return $this->asJson([
            'providers' => $this->blockProviders->getAll(true)
        ]);
    }

    public function actionBlocks(int $layout)
    {
        $this->requireAcceptsJson();
        return $this->asJson([
            'blocks' => $this->blocks->forLayout($layout)
        ]);
    }
}