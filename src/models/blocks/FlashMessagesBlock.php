<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\FlashMessagesBlockOptions;

/**
 * Block displaying the session flash messages
 */
class FlashMessagesBlock extends Block
{
    /**
     * @var string|false|null
     */
    protected $_notice = false;

    /**
     * @var string|false|null
     */
    protected $_error = false;

    /**
     * @var string
     */
    public static $handle = 'flash-messages';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Messages');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays system messages');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Will fetch the message from the \'notice\' and \'error\' session flash data');
    }

    /**
     * Get notice from session
     * 
     * @return ?string
     */
    public function getFlashNotice(): ?string
    {
        if ($this->_notice === false) {
            $this->_notice = \Craft::$app->session->getFlash('notice', null, $this->options->removeMessages);
        }
        return $this->_notice;
    }

    /**
     * Get error from session
     * 
     * @return string
     */
    public function getFlashError(): ?string
    {
        if ($this->_error === false) {
            $this->_error = \Craft::$app->session->getFlash('error', null, $this->options->removeMessages);
        }
        return $this->_error;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return ($this->flashNotice or $this->flashError);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return FlashMessagesBlockOptions::class;
    }
}
