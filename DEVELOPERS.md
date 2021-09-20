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
        BlogAssets::class
    ],
    '/$blog*$/' => [
        BlogAssets::class
    ],
    '/' => [
        HomeAssets::class
    ]
]
```

Bundle assets will be registered automatically, the '\*' array will be registered on every request.

By default, parent themes bundles will also be registered. This can be disabled with the property `$inheritsAssetBundles` of your theme class.

## Blocks

Blocks are provided by a block provider, each provider can define several blocks. This plugin comes with a default 'System' provider.

### Registering a new provider

Listen to the `REGISTER_BLOCK_PROVIDERS` event of the `BlockProvidersService` class :

```
Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProvidersEvent $event) {
    $event->add(new SystemBlockProvider);
});
```

Your block provider class must extends the `BlockProvider` class. Blocks defined by the providers are defined in their `$_definedBlocks` attribute.

An exception will be thrown if you register a provider which handle is already registered.

### Modifying a provider's blocks

You can modify blocks provided by a provider by responding to an event :

```
Event::on(SystemBlockProvider::class, BlockProviderInterface::REGISTER_BLOCKS, function (RegisterBlockProviderBlocks $event) {
    $event->blocks[] = MyBlock::class;
});
```

An exception will be thrown if you register 2 blocks with the same handle within a provider.

### Defining new blocks

New block classes must extends the `Block` class. You can override the `getOptionsModel` method to define more options, this method must return an instance that extends the `BlockOptions` class.

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpBlocksController::class, CpBlocksController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-block-option-components` and add your component to the `event.detail` variable. 

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/blockOptions/main.js)

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

### Creating/Deleting blocks programmatically

Example :

```
$defaultLayout = Themes::$plugin->layouts->getDefault('theme-handle');
$block = Themes::$plugin->blocks->createBlock([
    'provider' => 'system',
    'handle' => 'content'
]);
$defaultLayout->addBlock($block, 'region-handle');
Themes::$plugin->layouts->save($defaultLayout);

//This works too :

$defaultLayout = Themes::$plugin->layouts->getDefault('theme-handle');
$block = Themes::$plugin->blocks->createBlock([
    'provider' => 'system',
    'handle' => 'content',
    'region' => 'region-handle',
    'layout' => $defaultLayout
]);
Themes::$plugin->blocks->save($block);

```

## Fields

There are 10 types of fields defined by this plugin.

5 "new" fields, which can have their own displayers :

- Author : handles the author of an entry
- File : handles the file of an asset
- TagTitle : handles the title for a tag
- Title : handles the title of an entry/category
- UserInfo : handles the user info for user layouts

And 5 that handle Craft fields, those can't have their own displayers. Their displayers will display the Craft field associated with them :

- CraftField : handles most Craft fields (except Matrix and Table)
- Matrix : handles Craft matrix fields
- MatrixField : handles the field within a matrix
- Table : handles Craft table fields
- TableField : handles the fields within a table field

A field displayer defines how a field is rendered on the front end, each field displayer will handle one type of field. This is controlled by the method `getFieldTarget(): string` which will return either the class of the Craft field this displayer can handle, or the class of the themes field (like `Title`, `Author` etc).

### Define a new field

Register your php field class (implementing `FieldInterface`) by responding to the event :

```
Event::on(FieldsService::class, FieldsService::REGISTER_FIELDS, function (RegisterFieldsEvent $event) {
    $event->->add(MyField::class);
});
```

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-fields-components` and add your component and clone function to the `event.detail` variable.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/fields/main.js)

"new" fields (any field that doesn't extend from `CraftField`) can be created automatically on layouts if you return true to the method `shouldExistOnLayout(LayoutInterface $layout)`. By default this method returns false.  
This method won't have any effect for fields that extends `CraftField`, as they will be created automatically (assuming a Craft field exists on the category group/entry type).

### Define a new displayer

Register your displayer by listening to the `REGISTER_DISPLAYERS` event of the `FieldDisplayerService` class :

```
Event::on(FieldDisplayerService::class, FieldDisplayerService::REGISTER_DISPLAYERS, function (FieldDisplayerEvent $event) {
    $event->register(MyFieldDisplayer::class);
});
```

Your field displayer class must extend the `FieldDisplayer` class and define the field it can handle in its `getFieldTarget` method. 
Registering a field displayer with a handle already existing will **replace** the current displayer.

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-field-displayers-components` and add your component to the `event.detail` variable.  

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

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-file-displayers-components` and add your component to the `event.detail` variable.   

Validating your options and saving them will be handled automatically, as long as you have defined rules in your displayer options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/fileDisplayers/main.js)

## Templating

Templates are inherited, so if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

Each element of the page (layouts, regions, blocks, field and file displayers) templates can be overriden by your themes using a specific folder structure that allows much granularity.

Let's say you have an `entry` layout for a entry type `blog`, a region `header`, a view mode `small`, a block `latestBlogs`, a field `content`, a field displayer `redactor`, a file displayer `image` and a group `left`. The precedence of templates would look like this, by order of importance :

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
Groups:
```
groups/entry/blog/small/left.twig
groups/entry/blog/small/group.twig
groups/entry/blog/left.twig
groups/entry/blog/group.twig
groups/entry/left.twig
groups/entry/group.twig
groups/left.twig
groups/group.twig
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
- Groups : event `BEFORE_RENDERING_GROUP`  

Example :

```
Event::on(ViewService::class, ViewService::BEFORE_RENDERING_ASSET, function (RenderEvent $event) {
    $event->prependTemplate('myTemplate')
        ->addVariable('myVar', 'myValue');
});
```

Those events can also be used to override elements's classes and attributes :

```
Event::on(ViewService::class, ViewService::BEFORE_RENDERING_ASSET, function (RenderEvent $event) {
    $event->classes->add('my-class');
    $event->attributes->add('id', 'my-id');
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

If something looks off in the displays, you can always reinstall the themes data in the plugin settings. This will create missing fields/displayers for all themes and elements (entry types, category groups etc), and delete the orphans.

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

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpBlocksController::class, CpBlocksController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-block-strategy-components` and add your component to the `event.detail` variable. 

Validating your options and saving them will be handled automatically, as long as you have defined rules in your strategy options class.

Examples [here](https://github.com/ryssbowh/craft-themes/blob/v3/vue/src/blockStrategies/main.js)