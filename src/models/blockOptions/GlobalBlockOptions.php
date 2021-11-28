<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Options for the global block
 */
class GlobalBlockOptions extends BlockOptions
{
    public function defineOptions(): array
    {
        return [
            'set' => [
                'field' => 'select',
                'required' => true,
                'options' => $this->getSets(),
                'label' => \Craft::t('app', 'Global')
            ],
            'viewMode' => [
                'field' => 'fetchviewmode',
                'element' => 'from:#field-set:select',
                'layoutType' => LayoutService::GLOBAL_HANDLE,
                'label' => \Craft::t('app', 'View mode'),
                'required' => true
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['set', 'viewMode'], 'required'],
            [['set'], 'in', 'range' => array_keys($this->definitions['set']['options'])]
        ]);
    }

    /**
     * Get all global sets as array
     * 
     * @return array
     */
    protected function getSets(): array
    {
        $all = [];
        $sets = \Craft::$app->globals->getAllSets();
        foreach ($sets as $set) {
            $all[$set->uid] = $set->name;
        }
        asort($all);
        return $all;
    }
}
