<?php
namespace Ryssbowh\CraftThemes\interfaces;

/**
 * Interface for classes that have displays (view mode or groups for example)
 */
interface HasDisplaysInterface
{
    /**
     * Displays getter, will only fetch the root displays (that aren't in groups)
     * 
     * @return array
     */
    public function getDisplays(): array;

    /**
     * Display setter
     * 
     * @param ?array $displays
     */
    public function setDisplays(?array $displays);

    /**
     * Get all visible displays 
     * 
     * @return array
     */
    public function getVisibleDisplays(): array;

    /**
     * Returns all displays, in groups or not
     * 
     * @return array
     */
    public function getAllDisplays(): array;

    /**
     * Fetch a display by handle
     * Returns a field or a group type of display
     *
     * @param  string $handle
     * @param  bool   $onlyRoots Only look at root displays (not in groups)
     * @param  bool   $onlyVisibles Only look at visible displays
     * @return ?DisplayInterface
     */
    public function getDisplayByHandle(string $handle, bool $onlyRoots = true, bool $onlyVisibles = true): ?DisplayInterface;

    /**
     * Fetch a display by uid
     * Returns a field or a group type of display
     *
     * @param  string $uid
     * @param  bool   $onlyRoots Only look at root displays (not in groups)
     * @param  bool   $onlyVisibles Only look at visible displays
     * @return ?DisplayInterface
     */
    public function getDisplayByUid(string $uid, bool $onlyRoots = true, bool $onlyVisibles = true): ?DisplayInterface;

    /**
     * Fetch displays by handles
     * 
     * @param  array  $handles
     * @param  bool   $onlyRoots Only look at root displays (not in groups)
     * @param  bool   $onlyVisibles Only look at visible displays
     * @return array
     */
    public function getDisplaysByHandles(array $handles, bool $onlyRoots = true, bool $onlyVisibles = true): array;

    /**
     * Fetch displays by uids
     * 
     * @param  array  $uids
     * @param  bool   $onlyRoots Only look at root displays (not in groups)
     * @param  bool   $onlyVisibles Only look at visible displays
     * @return array
     */
    public function getDisplaysByUids(array $uids, bool $onlyRoots = true, bool $onlyVisibles = true): array;
}