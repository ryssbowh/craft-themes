# Craft themes

## 3.0 breaking changes

- Main class must inherit `Ryssbowh\CraftThemes\models\ThemePlugin`

## Creating a new Theme

Create a new plugin, it's main class must extend `Ryssbowh\CraftThemes\models\ThemePlugin`.

You could for example create a `themes` folder at the root, and add it as a composer repository by modifying the root `composer.json` :

```
"repositories": [
    {
        "type": "path",
        "url": "themes/*",
        "options": {
            "symlink": true
        }
    }
]
```

You can then require your theme as any other package.  
When it's installed, enable it in the backend.

## Root templates folder

It is recommended to not use the root `templates` folder when using themes, if some templates are defined both in this folder and in a theme, the root templates folder will take precedence.

A theme **cannot** override templates that have a namespace (ie other plugin templates), unless they register their templates roots with the '' (empty string) key.

## Inheritance

Themes can extend each other with the method `getExtends(): bool` of their main class.  
Parent themes will be installed automatically when installing a theme in the backend.

### Templates 

Templates are inherited, so if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

### Assets (images, fonts etc)

Assets can be inherited through the twig function `theme_url`.  
If you have an `image.jpg` defined in your theme in the `images` folder, you can publish it with `theme_url('images/image.jpg')`  

If you require an asset and the file is not present in your theme, it will look in the parent theme (if defined).

This inheritance can be disabled with the property `$inheritsAssets` of your theme class.

## Partial themes

Define a partial theme with the method `isPartial(): bool` of the plugin class.

## Twig

`craft.themes.layouts` : Layouts service 
`craft.themes.viewModes` : View mode service 
`craft.themes.registry` : Theme registry 
`craft.themes.view` : Theme view service 
`craft.themes.current` : Current theme

**Deprecated**

You have access to two new variables in your templates :

`themesRegistry` : `Ryssbowh\CraftThemes\services\ThemesRegistry` instance  
`theme` : Current theme instance. This is not set if no theme is set.  

## Asset Bundles

Asset bundles can be defined in your theme class, in the `$assetBundles` property, it's an array indexed by the url path :
```
[
	'*' => [
		CommonAssets::class
	],
	'blog' => [
		BlogAsset::class
	]
]
```

Bundle assets will be registered automatically, the '\*' will be registered on every page.

By default, parent themes bundles will also be registered. This can be disabled with the property `$inheritsAssetBundles` of your theme class.

## Aliases

4 new aliases are set :

`@themePath` : Base directory of the current theme. This is not set if no theme is set.
`@@themeWeb` : Base web url for the current theme. This is not set if no theme is set.

And two that are not used by the system, but could be useful if you're using a tool (such as webpack, gulp etc) to build your assets :

`@themesWebPath` : Web directory for themes, equivalent to `@root/web/themes`  
`@themeWebPath` : Web directory for current theme. This is not set if no theme is set. 