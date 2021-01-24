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
	 * Bundle assets to register
	 * 
	 * @return array
	 */
	public function getBundleAssets(): array;

	/**
	 * Does this theme extends another one
	 * 
	 * @return ?string
	 */
	public function getExtends(): ?string;

	/**
	 * Register this theme's assets in view
	 */
	public function registerAssets();

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
}