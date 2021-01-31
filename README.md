# Craft themes

Tired of re-making your front-ends all over again when a lot of it could be reused ?

This package allows you to create themes that can extend from each other and that can be used as composer packages.

A rule system allows you to set which theme will be used for which site, language or url path.

## Getting started

Create the folder `themes/{name}`, add a theme class to it, [an example here](https://github.com/ryssbowh/example-theme/blob/master/Theme.php)
Create the folder `themes/{name}/templates`

That's enough to define a theme that you can enable in the backend.

And read on...

## Templates 

Templates are inherited, that's the whole point isn't it ?

So if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

If the template exists in the Craft root template folder, it will bypass the theme engine and will be loaded first.

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

And one new function :

Assets like images, fonts etc can be inherited through the function `theme_url`.  
If you have an `image.jpg` defined in your theme in the `images` folder, you can publish it with `theme_url('images/image.jpg')`

If you require an asset and the file is not present in your theme, it will look in the parent theme (if defined).

The inheritance can be disabled with the property `$inheritsAssets` of your theme class.

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

## Inheritance

Themes can extend each other with the property `$extends` of their Theme class.

## Composer

If you bundle a theme into a composer package, 2 things are required :

- Your composer.json must have the type `craft-theme`.
- Your composer.json must have a parameter `handle` in the `extra` section. If this is not present, your theme will have its package name as handle.

Assuming your package is accessible through composer, you can require it like any other package, it will be installed in the `themes` folder.

You will need to empty the themes cache after creating a new theme, through the backend or with `php craft clear-caches/themes-cache`

[See an example](https://github.com/ryssbowh/example-theme/blob/master/composer.json)

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

Craft 3.5

## Roadmap

- bundleAssets as regular expressions paths
- preprocess functions