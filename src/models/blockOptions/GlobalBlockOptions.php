<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Options for the global block
 */
class GlobalBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'set' => [
                'field' => 'select',
                'required' => true,
                'options' => $this->getSets(),
                'label' => \Craft::t('themes', 'Global')
            ],
            'viewMode' => [
                'field' => 'fetchviewmode',
                'element' => 'from:#field-set:select',
                'layoutType' => LayoutService::GLOBAL_HANDLE,
                'label' => \Craft::t('themes', 'View mode'),
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
            [['set'], 'in', 'range' => array_keys($this->definitions['set']['options'])],
            ['viewMode', function () {
                try {
                    Themes::$plugin->viewModes->getByUid($this->viewMode);
                } catch (ViewModeException $e) {
                    $this->addError('viewMode', \Craft::t('themes', 'View mode is invalid'));
                }
            }]
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
