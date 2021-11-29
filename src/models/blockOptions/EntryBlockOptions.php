<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the entry block
 */
class EntryBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'entries' => [
                'field' => 'elements',
                'elementType' => 'entries',
                'addElementLabel' => \Craft::t('app', 'Add an entry'),
                'required' => true,
                'label' => \Craft::t('app', 'Entries'),
                'saveInConfig' => false
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'entries' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['entries'], 'required'],
            ['entries', function () {
                if (!is_array($this->entries)) {
                    $this->addError('entries', \Craft::t('themes', 'Invalid entries'));
                }
                foreach ($this->entries as $array) {
                    try {
                        Themes::$plugin->viewModes->getByUid($array['viewMode']);
                    } catch (ViewModeException $e) {
                        $this->addError('entries', [$array['id'] => \Craft::t('themes', 'View mode is invalid')]);
                    }
                }
            }]
        ]);
    }
}
