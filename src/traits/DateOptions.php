<?php
namespace Ryssbowh\CraftThemes\traits;

use craft\i18n\FormatConverter;

/**
 * Trait to be used for configurable options that handle dates
 */
trait DateOptions
{
    /**
     * @inheritDoc
     */
    public function defineDateOptions(): array
    {
        $dt = new \DateTime;
        $dt->setTimestamp(1638195320);
        $formats = [];
        foreach ($this->getFormats() as $format) {
            $formats[$format] = \IntlDateFormatter::formatObject($dt, $format, \Craft::$app->locale);
        }
        $formats['custom'] = \Craft::t('themes', 'Custom');
        return [
            'format' => [
                'field' => 'select',
                'options' => $formats,
                'required' => true,
                'label' => \Craft::t('themes', 'Format')
            ],
            'custom' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Custom'),
                'placeholder' => 'dd/LL/y kk:mm:ss',
                'instructions' => \Craft::t('themes', 'ICU date format, view documentation {tag}here{endtag}', ['tag' => '<a href="https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax" target="_blank">', 'endtag' => '</a>'])
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDateRules(): array
    {
        return [
            [['format', 'custom'], 'string'],
            ['format', 'required'],
            ['format', 'in', 'range' => array_keys($this->definitions['format']['options'])],
            ['custom', 'required', 'when' => function ($model) {
                return $model->format == 'custom';
            }],
        ];
    }
}