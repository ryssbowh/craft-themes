# Craft themes

### This is the 2.0 documentation, find the 1.0 documentation [there](https://github.com/ryssbowh/example-theme/blob/master/README1.md)

Tired of re-making your front-ends all over again when a lot of it could be reused ?

A theme is just another Craft plugin, it can inherit another theme and has the same functions as any other plugin, settings, migrations etc. 

A rule system allows you to set which theme will be used for which site, language or url path.

## 2.0 breaking changes

- composer.json type must be `craft-plugin`
- Main class must inherit `Ryssbowh\CraftThemes\ThemePlugin`
- Main class method `getName()` has been removed
- Main class method `getHandle()` has been removed

## Getting started

Create a new plugin, it's main class must extend `Ryssbowh\CraftThemes\ThemePlugin`.

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

## Inheritance

Themes can extend each other with the method `getExtends(): bool` of their main class.  
Parent themes will be installed automatically when installing a theme in the backend.

### Templates 

Templates are inherited, that's the whole point isn't it ?

So if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

### Assets (images, fonts etc)

Assets can be inherited through the twig function `theme_url`.  
If you have an `image.jpg` defined in your theme in the `images` folder, you can publish it with `theme_url('images/image.jpg')`  

If you require an asset and the file is not present in your theme, it will look in the parent theme (if defined).

This inheritance can be disabled with the property `$inheritsAssets` of your theme class.

## Partial theme

A partial theme will not be available to select in the backend, but it can be inherited from. Define a partial theme with the method `isPartial(): bool` of the main class.

## Rules settings

Define rules in the settings to load the theme you want according to 3 parameters :
- the current site
- the current language
- the current url path, this can also be a regular expression if enclosed in slashes. example `/^blog*/`. Enter `/` for the homepage.

The first rule that match will define which theme will be used. Organise your rules to have the most specific first.

If no rules match, the default theme will be used.

## Twig

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

`@themesPath` : Base directory for themes  
`@themePath` : Base directory of the current theme. This is not set if no theme is set.

And two that are not used by the system, but could be useful if you're using a tool (such as webpack, gulp etc) to build your assets :

`@themesWebPath` : Web directory for themes, equivalent to `@root/web/themes`  
`@themeWebPath` : Web directory for current theme. This is not set if no theme is set.  

## Installation

run `composer require ryssbowh/craft-themes`  
Add a rule in the settings to load a theme or set a default theme.  

## Requirements

Craft 3.5 or over

## Roadmap

- bundleAssets as regular expressions paths  
- preprocess functions