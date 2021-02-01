<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface ThemeInterface 
{
	/**
	 * THeme name
	 * 
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Theme handle
	 * 
	 * @return string
	 */
	public function getHandle(): string;

	/**
	 * Relative templates path
	 * 
	 * @return string
	 */
	public function getTemplatePath(): string;

	/**
	 * Absolute template paths, including those of the parent(s)
	 * 
	 * @return string
	 */
	public function getTemplatePaths(): array;

	/**
	 * Register this theme's assets in view for a specific path
	 */
	public function registerAssetBundles(string $urlPath);

	/**
	 * Get theme parent
	 * 
	 * @return ?ThemeInterface
	 */
	public function getParent(): ?ThemeInterface;

	/**
	 * Does this theme inherits another theme
	 * 
	 * @return boolean
	 */
	public function hasParent(): bool;

	/**
	 * Get an url for an theme asset.
	 * 
	 * @param  string $path
	 * @return string
	 */
	public function getAssetUrl(string $path): string;

	/**
	 * Get theme's regions
	 * 
	 * @return array
	 */
	public function getRegions(): array;
}