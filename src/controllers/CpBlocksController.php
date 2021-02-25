<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpBlocksController extends Controller
{
    /**
     * Get all block providers as json
     * 
     * @return Response
     */
    public function actionProviders()
    {
        $this->requireAcceptsJson();
        return $this->asJson([
            'providers' => $this->blockProviders->getAll(true)
        ]);
    }

    /**
     * Get all blocks for a layout as json
     * 
     * @param  int    $layout
     * @return Response
     */
    public function actionBlocks(int $layout)
    {
        $this->requireAcceptsJson();
        return $this->asJson([
            'blocks' => $this->blocks->forLayout($layout)
        ]);
    }
}