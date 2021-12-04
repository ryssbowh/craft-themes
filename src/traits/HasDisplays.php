<?php
namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\interfaces\DisplayInterface;

/**
 * Trait to be used for classes that contains displays (view modes or groups for instance)
 */
trait HasDisplays
{
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
}