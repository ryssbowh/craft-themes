<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use craft\fields\MissingField;

/**
 * Handles a missing field
 *
 * @since 3.1.0
 */
class Missing extends CraftField
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'missing';
    }

    /**
     * @inheritDoc
     */
    public static function forField(): string
    {
        return MissingField::class;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return \Craft::t('themes', 'Missing field');
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function create(array $config): FieldInterface
    {
        $class = get_called_class();
        if ($config['craft_field_id']) {
            //Make sure we have the proper type here, maybe the missing field is not missing anymore
            $field = \Craft::$app->fields->getFieldById($config['craft_field_id']);
            $type = Themes::$plugin->fields->getTypeForCraftField($field);
            $config['type'] = $type;
            $class = Themes::$plugin->fields->getFieldClassByType($type);
        }
        $config['displayerHandle'] = $config['displayerHandle'] ?? Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? '';
        $field = new $class;
        $field->populateFromData($config);
        return $field;
    }
}