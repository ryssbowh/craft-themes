<?php

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var ?string
     */
    public $default;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rules', function ($attribute, $params, $validator) {
                foreach ($this->rules as $index => $rule) {
                    if ($rule['type'] == 'url' and trim($rule['url'] == '')) {
                        $this->addError('rules', $index.':2');
                    }
                    switch ($rule['type']) {
                        case 'url':
                            unset($this->rules[$index]['site']);
                            unset($this->rules[$index]['language']);
                            break;
                        case 'site':
                            unset($this->rules[$index]['url']);
                            unset($this->rules[$index]['language']);
                            break;
                        default:
                            unset($this->rules[$index]['site']);
                            unset($this->rules[$index]['url']);
                            break;
                    }
                }
            }],
            ['default', 'string']
        ];
    }

    /**
     * Get the theme handle for a site
     * 
     * @param  string $uid
     * @return string
     */
    public function getHandle(string $uid): string
    {
        return $this->themes[$uid] ?? '';
    }
}
