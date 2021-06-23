# Developers

## 3.0 breaking changes

- Main class must inherit `Ryssbowh\CraftThemes\models\ThemePlugin`

### Deprecated

The following twig variables are deprecated and will be removed in a future version :

`themesRegistry` : `Ryssbowh\CraftThemes\services\ThemesRegistry` instance  
`theme` : Current theme instance.

## Themes

### Inheritance

Themes can extend each other with the method `getExtends(): bool` of their main class.  
Parent themes will be installed automatically when installing a theme in the backend.

### Assets (images, fonts etc)

Assets can be inherited through the twig function `theme_url`.  
If you have an `image.jpg` defined in your theme in the `images` folder, you can publish it with `theme_url('images/image.jpg')`  

If you require an asset and the file is not present in your theme, it will look in the parent theme (if defined).

This inheritance can be disabled with the property `$inheritsAssets` of your theme class.

### Partial themes

Define a partial theme with the method `isPartial(): bool` of the plugin class.

### Creating a new Theme

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

### Asset Bundles

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

Bundle assets will be registered automatically, the '\*' array will be registered on every request.

By default, parent themes bundles will also be registered. This can be disabled with the property `$inheritsAssetBundles` of your theme class.

## Blocks

Blocks are provided by a block provider, each provider can define several blocks. This plugin comes with a default System provider.

### Registering a new provider

Listen to the `REGISTER_BLOCK_PROVIDERS` event of the `BlockProvidersService` class :

```
Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProvidersEvent $event) {
    $event->add(new SystemBlockProvider);
});
```

Your block provider class must extends the `BlockProvider` class. Blocks defined by the providers are defined in their `$_definedBlocks` attribute.

An exception will be thrown if you register a provider which handle is already registered.

### Defining new blocks

New block classes must extends the `Block` class. You can override the `getOptionsModel` method to define more options, this method must return a class that extends the `BlockOptions` class.

To display your options in the backend you need to hook in the Vue backend system, by defining a new Vue component on the `window.themesBlockOptionComponents` variable.  
This variable will be defined after the `BlockOptionsAsset` has been registered, so if you use an asset bundle to register your js, make sure your bundle depends on that. 

Examples in `vue/src/blockOptions/main.js`.

The key is built like so : {provider handle}-{block handle}.

You can pass data from your block to Vue by overriding the `fields` method of your block class :
```
class MyBlock extends Block
{
    public function getMyVariable()
    {
        return 'my value';
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['myVariable']);
    }
}
```
Validating your options and saving them will be handled automatically, as long as you have defined rules in your block options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/blockOptions/main.js)

## Fields

There are 8 types of fields defined by this plugin :

- Title : handles the title of an entry/category
- Author : handles the author of a entry/category
- File : handles the file of an asset
- Matrix : handles Craft matrix fields
- MatrixField : handles the field within a matrix
- Table : handles Craft table fields
- TableField : handles the fields within a table field
- CraftField : handles all the other Craft fields

A field displayer defines how a field is rendered on the front end, each field displayer will handle one type of field. This is controlled by the method `getFieldTarget(): string` which will return either the class of the Craft field this displayer can handle, or the class of the themes field (like `Title`).

### Define a new field

Register your php field class (implementing `FieldInterface`) by responding to the event :

```
Event::on(FieldsService::class, FieldsService::REGISTER_FIELDS, function (RegisterFieldsEvent $event) {
    $event->->add(MyField::class);
});
```

To hook in the backend Vue system, add a new component to the `window.themesFieldsComponents` variable. 
This variable will be defined after the `FieldsAsset` has been registered, so if you use an asset bundle to register your js, make sure your bundle depends on that.

Your field can be created automatically on layout creation if you return true to the method `shouldExistOnLayout(LayoutInterface $layout)`. This is useful for fields that aren't Craft fields (like `Title`) and need to exist on some Layouts. By default this method returns false.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/fields/main.js)

### Define a new displayer

Register your displayer by listening to the `REGISTER_DISPLAYERS` event of the `FieldDisplayerService` class :

```
Event::on(FieldDisplayerService::class, FieldDisplayerService::REGISTER_DISPLAYERS, function (FieldDisplayerEvent $event) {
    $event->register(MyFieldDisplayer::class);
});
```

Your field displayer class must extend the `FieldDisplayer` class and define the field it can handle in its `getFieldTarget` method. 
Registering a field displayer with a handle already existing will **replace** the current displayer.

To hook in the backend Vue system, add a new component to the `window.themesFieldDisplayersComponents` variable. 
This variable will be defined after the `FieldDisplayerAsset` has been registered, so if you use an asset bundle to register your js, make sure your bundle depends on that. 

Validating your options and saving them will be handled automatically, as long as you have defined rules in your displayer options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/fieldDisplayers/main.js)

## File displayers

A file displayer defines how an asset file is rendered on the front end. Each displayer can handle one or several asset kinds.

### Define a new displayer

Register your displayer by listening to the `REGISTER_DISPLAYERS` event of the `FileDisplayerService` class :

```
Event::on(FileDisplayerService::class, FileDisplayerService::REGISTER_DISPLAYERS, function (FileDisplayerEvent $event) {
    $event->register(MyFileDisplayer::class);
});
```

Your file displayer class must extend the `FileDisplayer` class and define one or several asset kinds in the `getKindTargets` method. The '\*' can be used to indicate this displayer can handle all asset kinds.  
Registering a file displayer with a handle already existing will **replace** the current displayer.

Same as with blocks, to hook in the backend Vue system, add a new component to the `window.themesFileDisplayersComponents` variable. 
This variable will be defined after the `FileDisplayerAsset` has been registered, so if you use an asset bundle to register your js, make sure your bundle depends on that. 

Validating your options and saving them will be handled automatically, as long as you have defined rules in your displayer options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/fileDisplayers/main.js)

## Templating

Templates are inherited, so if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

Each element of the page (layouts, regions, blocks, field and file displayers) templates can be overriden by your themes using a specific folder structure that allows much granularity.

Let's say you have an `entry` layout for a entry type `blog`, a region `header`, a view mode `small`, a block `latestBlogs`, a field `content`, a field displayer `redactor` and a file displayer `image`. The precedence of templates would look like this, by order of importance :

Layouts :

```
layouts/entry/blog/small.twig
layouts/entry/blog.twig
layouts/entry.twig
layouts/layout.twig
```
Regions :
```
regions/entry/blog/region-header.twig
regions/entry/blog/region.twig
regions/entry/region-header.twig
regions/entry/region.twig
regions/region-header.twig
regions/region.twig
```
Blocks :
```
blocks/entry/blog/header/latestBlogs.twig
blocks/entry/blog/latestBlogs.twig
blocks/entry/latestBlogs.twig
blocks/latestBlogs.twig
```
Field displayers :
```
fields/entry/blog/small/redactor-content.twig
fields/entry/blog/small/redactor.twig
fields/entry/blog/redactor-content.twig
fields/entry/blog/redactor.twig
fields/entry/redactor-content.twig
fields/entry/redactor.twig
fields/redactor-content.twig
fields/redactor.twig
```
File displayers :
```
assets/entry/blog/small/image.twig
assets/entry/blog/image.twig
assets/entry/image.twig
assets/image.twig
```

More templates and variables can be defined by listening to events on the `ViewService` class :

- Layouts : event `BEFORE_RENDERING_LAYOUT`  
- Assets : event `BEFORE_RENDERING_ASSET`  
- Fields : event `BEFORE_RENDERING_FIELD`  
- Blocks : event `BEFORE_RENDERING_BLOCK`  
- Regions : event `BEFORE_RENDERING_REGION`  

Example :

```
Event::on(ViewService::class, ViewService::BEFORE_RENDERING_ASSET, function (RenderEvent $event) {
    $event->prependTemplate('myTemplate')
        ->addVariable('myVar', 'myValue');
});
```

### Dev mode

The available templates and variables can be printed as html comments by enabling the option in the themes plugin settings.

### Root templates folder

It is recommended to not use the root `templates` folder when using themes, if some templates are defined both in this folder and in a theme, the root templates folder will take precedence.

A theme **can't** override templates that have a namespace (ie other plugin templates), unless they register their templates roots with the '' (empty string) key.

## Eager loading

By default, when a layout is rendered it will eager load every field it contains, this can be disabled in the settings.  
All the default templates defined by this plugin expect fields to be eager loaded, if you switch off that feature you need to make sure every template is overriden.

## Twig

`craft.themes.layouts` : Layouts service  
`craft.themes.viewModes` : View mode service  
`craft.themes.registry` : Theme registry  
`craft.themes.view` : Theme view service  
`craft.themes.current` : Current theme 

## Aliases

4 new aliases are set :

`@themePath` : Base directory of the current theme. This is not set if no theme is set.

And three that are not used by the system, but could be useful if you're using a tool (such as webpack, gulp etc) to build your assets :

`@themesWebPath` : Web directory for themes, equivalent to `@root/web/themes`  
`@themeWebPath` : Web directory for current theme. This is not set if no theme is set.  
`@themeWeb` : Base web url for the current theme. This is not set if no theme is set.

## Reinstall data

If something looks off in the displays, you can always reinstall the themes data in the plugin settings. This will create missing displayers for all themes and elements (entry types, category groups etc), and delete the orphans.

## Caching

### Rules cache

Theme resolution rules will be cached by default on environments where devMode is disabled. If you change rules on a production environment, clear the cache : `./craft clear-caches/themes-rules-cache`

It can be overriden by creating the `config/themes.php` file :

```
<?php 

return [
    'rulesCache' => false
];
```

### Template cache

Template resolution will be cached by default on environments where devMode is disabled. So it's a good idea to clear these cache when deploying to a production environment : `./craft clear-caches/themes-template-cache`

It can be overriden by creating the `config/themes.php` file :

```
<?php 

return [
    'templateCache' => false
];
```

### Block cache

Block cache will be enabled by default on environments where devMode is disabled.

It can be overriden by creating the `config/themes.php` file :

```
<?php 

return [
    'blockCache' => false
];
```

Each block can have a block strategy that defines how it will be cached. Add new strategies by responding to the event :

```
Event::on(BlockCacheService::class, BlockCacheService::REGISTER_STRATEGIES, function (RegisterBlockCacheStrategies $event) {
    $event->add(new MyCacheStrategy);
});
```

To hook in the backend Vue system, add a new component to the `window.themesBlockStrategyComponents` variable. 
This variable will be defined after the `BlockOptionsAsset` has been registered, so if you use an asset bundle to register your js, make sure your bundle depends on that. 

Validating your options and saving them will be handled automatically, as long as you have defined rules in your strategy options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/blockStrategies/main.js)

Block caching uses Craft internal cache tagging system so cache will be automatically invalidated when elements used within a block are changed.