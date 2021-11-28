<?php
namespace Ryssbowh\CraftThemes\traits;

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
        return [
            'format' => [
                'field' => 'select',
                'options' => array_merge($this->getFormats(), [
                    'custom' => \Craft::t('themes', 'Custom')
                ]),
                'required' => true,
                'label' => \Craft::t('themes', 'Format')
            ],
            'custom' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Custom'),
                'instructions' => \Craft::t('themes', 'View available formats {tag}here{endtag}', ['tag' => '<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">', 'endtag' => '</a>'])
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