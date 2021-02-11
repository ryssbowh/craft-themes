<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface SuggestsTemplates 
{	
	/**
	 * Get templates paths
	 * 
	 * @return array
	 */
	public function getTemplateSuggestions(): array;
}