<?php
namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\interfaces\DisplayInterface;

/**
 * Trait to be used for classes that contains displays (view modes or groups for instance)
 */
trait HasDisplays
{
    /**
     * @var DisplayInterface[]
     */
    protected $_displays;

    /**
     * @inheritDoc
     */
    public function getDisplays(): array
    {
        if (is_null($this->_displays)) {
            $this->_displays = $this->loadDisplays();
        }
        return $this->_displays;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleDisplays(): array
    {
        return array_filter($this->displays, function ($display) {
            return $display->item->isVisible();
        });
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        foreach ($this->displays as $display) {
            if ($display->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->displays as $display) {
            $display->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors();
        foreach ($this->displays as $index => $display) {
            if ($display->hasErrors()) {
                $errors['displays'][$index] = $display->getErrors();
            }
        }
        if ($attribute === 'displays') {
            return $errors['displays'] ?? [];
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayByUid(string $uid, bool $onlyRoots = true, bool $onlyVisibles = true): ?DisplayInterface
    {
        $from = $this->displays;
        if (!$onlyRoots) {
            $from = $this->allDisplays;
        }
        $displays = array_filter($from, function ($display) use ($uid, $onlyVisibles) {
            if ($display->uid == $uid) {
                if ($onlyVisibles and !$display->item->isVisible()) {
                    return false;
                }
                return true;
            }
            return false;
        });
        return $displays[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayByHandle(string $handle, bool $onlyRoots = true, bool $onlyVisibles = true): ?DisplayInterface
    {
        $from = $this->displays;
        if (!$onlyRoots) {
            $from = $this->allDisplays;
        }
        $displays = array_filter($from, function ($display) use ($handle, $onlyVisibles) {
            if ($display->handle == $handle) {
                if ($onlyVisibles and !$display->item->isVisible()) {
                    return false;
                }
                return true;
            }
            return false;
        });
        return $displays[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getDisplaysByUids(array $uids, bool $onlyRoots = true, bool $onlyVisibles = true): array
    {
        $from = $this->displays;
        if (!$onlyRoots) {
            $from = $this->allDisplays;
        }
        return array_filter($from, function ($display) use ($uids, $onlyVisibles) {
            if (in_array($display->uid, $uids)) {
                if ($onlyVisibles and !$display->item->isVisible()) {
                    return false;
                }
                return true;
            }
            return false;
        });
    }

    /**
     * @inheritDoc
     */
    public function getDisplaysByHandles(array $handles, bool $onlyRoots = true, bool $onlyVisibles = true): array
    {
        $from = $this->displays;
        if (!$onlyRoots) {
            $from = $this->allDisplays;
        }
        return array_filter($from, function ($display) use ($handles, $onlyVisibles) {
            if (in_array($display->handle, $handles)) {
                if ($onlyVisibles and !$display->item->isVisible()) {
                    return false;
                }
                return true;
            }
            return false;
        });
    }

    /**
     * Load displays from db
     * 
     * @return array
     */
    abstract protected function loadDisplays(): array;

    /**
     * @inheritDoc
     */
    abstract public function setDisplays(?array $displays);
}