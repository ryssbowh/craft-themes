# Developers

## Update from 2.0 breaking changes :

- Main plugin class must inherit `Ryssbowh\CraftThemes\base\ThemePlugin`

### Deprecated

The following twig variables are deprecated and will be removed in a future version :

`themesRegistry` : `Ryssbowh\CraftThemes\services\ThemesRegistry` instance  
`theme` : Current theme instance.

## Themes

### Inheritance

Themes can extend each other with the method `getExtends(): string` of their main class.  
Parent themes will be installed automatically when installing a theme in the backend, just make sure they are required in the composer.json of the child theme.

### Assets (images, fonts etc)

Assets can be inherited through the twig function `theme_url`.  
If you have an `image.jpg` defined in your theme in the `images` folder, you can publish it with `theme_url('images/image.jpg')`  

If you require an asset and the file is not present in your theme, it will look in the parent theme (if defined).

This inheritance can be disabled with the property `$inheritsAssets` of your theme class.

### Partial themes

Define a partial theme with the method `isPartial(): bool` of the plugin class.

### Creating a new Theme

Create a new plugin, it's main class must extend `Ryssbowh\CraftThemes\models\ThemePlugin`.

You could for example create a `themes` folder at the root, and add it as a composer repository by adding to the root `composer.json` :

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
    '/^blog*$/' => [
        BlogAssets::class
    ],
    '/' => [
        HomeAssets::class
    ]
]
```

Bundle assets will be registered automatically, the '\*' array will be registered on every request.

By default, parent theme bundles will also be registered. This can be disabled with the property `$inheritsAssetBundles` of your theme class.

### Setting theme manually

You can set a theme manually on the theme registry : `Themes::$plugin->registry->setCurrent('theme-handle')`. Because Template roots can only be registered once on Craft, you **must** do this before the View template roots are registered for the mode site (`View::TEMPLATE_MODE_SITE`) or an exception will be thrown.

## Layouts (Pro)

Templates are created by the system automatically, their types as mentionned in this readme are as follows :

- Default : `default` or `LayoutService::DEFAULT_HANDLE`
- Entry types : `entry` or `LayoutService::ENTRY_HANDLE`
- Category groups : `category` or `LayoutService::CATEGORY_HANDLE`
- Global sets : `global` or `LayoutService::GLOBAL_HANDLE`
- Tag groups : `tag` or `LayoutService::TAG_HANDLE`
- Users : `user` or `LayoutService::USER_HANDLE`
- Volumes : `volume` or `LayoutService::VOLUME_HANDLE`
- Custom : `custom` or `LayoutService::CUSTOM_HANDLE`

### Custom layouts

You can add a custom programmaticaly layout by doing :

```
$layout = Themes::$plugin->layouts->createCustom([
    'name' => 'My Layout',
    'elementUid' => 'handle',
    'themeHandle' => 'theme-handle'
]);
Themes::$plugin->layouts->save($layout);
```
Render it :
```
{% set layout = craft.themes.layouts.getCustom(craft.themes.current, 'handle') %}
{{ layout.renderRegions()|raw }}

```
And delete it :
```
Themes::$plugin->layouts->deleteCustom($layout);

```
To render the content block for a custom template, follow the block template precedence described below.

## Blocks (Pro)

Blocks are provided by a block provider, each provider can define several blocks. This plugin comes with two providers : `System` and `Forms`.

### Registering a new provider

Listen to the `REGISTER_BLOCK_PROVIDERS` event of the `BlockProvidersService` class :

```
Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProvidersEvent $event) {
    $event->add(new SystemBlockProvider);
});
```

Your block provider class must implements `BlockProviderInterface`. Blocks defined by the providers are defined in their `$_definedBlocks` attribute.

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

New block classes must implements `BlockInterface`. You can override the `getOptionsModel` method to define more options, this method must return an instance that implements `BlockOptionsInterface`.

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpBlocksController::class, CpBlocksController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-block-option-components` and add your component to the `event.detail` variable. 

Examples [here](vue/src/blockOptions/main.js)

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

## Fields (Pro)

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
    $event->add(MyField::class);
});
```

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-fields-components` and add your component and clone function to the `event.detail` variable.

Examples [here](vue/src/fields/main.js)

"new" fields (any field that doesn't extend from `CraftField`) can be created automatically on layouts if you return true to the method `shouldExistOnLayout(LayoutInterface $layout)`. By default this method returns false.  
This method won't have any effect for fields that extends `CraftField`, as they will be created automatically (assuming a Craft field exists on the category group/entry type).

### Define a new displayer

Register your displayer by listening to the `REGISTER_DISPLAYERS` event of the `FieldDisplayerService` class :

```
Event::on(FieldDisplayerService::class, FieldDisplayerService::REGISTER_DISPLAYERS, function (FieldDisplayerEvent $event) {
    $event->register(MyFieldDisplayer::class);
});
```

Your field displayer class must implements `FieldDisplayerInterface` class and define the field it can handle in its `getFieldTarget` method. 
Registering a field displayer with a handle already existing will **replace** the current displayer.

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-field-displayers-components` and add your component to the `event.detail` variable.  

Validating your options and saving them will be handled automatically, as long as you have defined rules in your displayer options class.

Examples [here](vue/src/fieldDisplayers/main.js)

## File displayers (Pro)

A file displayer defines how an asset file is rendered on the front end. Each displayer can handle one or several asset kinds.

### Define a new displayer

Register your displayer by listening to the `REGISTER_DISPLAYERS` event of the `FileDisplayerService` class :

```
Event::on(FileDisplayerService::class, FileDisplayerService::REGISTER_DISPLAYERS, function (FileDisplayerEvent $event) {
    $event->register(MyFileDisplayer::class);
});
```

Your file displayer class must extend the `FileDisplayerInterface` class and define one or several asset kinds in the `getKindTargets` method. The '\*' can be used to indicate this displayer can handle all asset kinds.  
Registering a file displayer with a handle already existing will **replace** the current displayer.

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
respond to the js event `register-file-displayers-components` and add your component to the `event.detail` variable.   

Validating your options and saving them will be handled automatically, as long as you have defined rules in your displayer options class.

Examples [here](vue/src/fileDisplayers/main.js)

## Templating

Templates are inherited, so if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

Each element of the page (layouts, regions, blocks, field and file displayers) templates can be overriden by your themes using a specific folder structure that allows much granularity.

### Layouts (Pro)

There are two ways to render layouts, regions or displays. The method `Layout::render(Element $element, string $viewMode)` will render the displays, the other `$layout->renderRegions()` the regions.

Rendering the regions will call a special template defined by the theme in `ThemePlugin::getRegionsTemplate()`, by default this equals to `regions`.

You can set any templates to your category groups and sections, if a layout matches for that request, the layout associated will be added to the variables. In your template you simply need to call `{{ layout.renderRegions()|raw }}`. The view mode for such a request is always the default one.

If you have a layout of type `entry` for an entry type `blog` and a view mode `default`, the layout templates will take this precedence :

```
layouts/entry/blog/default.twig
layouts/entry/blog.twig
layouts/entry.twig
layouts/layout.twig
```
### Regions (Pro)

If you have a region `header` for a layout of type `entry` for an entry type `blog`, the region templates will take this precedence :
```
regions/entry/blog/region-header.twig
regions/entry/blog/region.twig
regions/entry/region-header.twig
regions/entry/region.twig
regions/region-header.twig
regions/region.twig
```
If you have a region `header` for a custom layout of handle `my-layout`, the region templates will take this precedence :
```
regions/custom/my-layout/region-header.twig
regions/custom/my-layout/region.twig
regions/custom/region-header.twig
regions/custom/region.twig
regions/region-header.twig
regions/region.twig
```
### Blocks (Pro)

If you have a block `latestBlogs` of a provider `system` for a layout of type `entry` for an entry type `blog` situated in a region `header`, the block templates will take this precedence :
```
blocks/entry/blog/header/system_latestBlogs.twig
blocks/entry/blog/system_latestBlogs.twig
blocks/entry/system_latestBlogs.twig
blocks/system_latestBlogs.twig
```
### Fields (Pro)

If you have a field displayer `redactor` for a field `content` on a layout of type `entry` for a entry type `blog` in a view mode `small`, the field templates will take this precedence :
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
### Groups (Pro)

If you have a group `left` on a layout of type `entry` for a entry type `blog` in a view mode `small`, the group templates will take this precedence :
```
groups/entry/blog/small/group-left.twig
groups/entry/blog/small/group.twig
groups/entry/blog/group-left.twig
groups/entry/blog/group.twig
groups/entry/group-left.twig
groups/entry/group.twig
groups/group-left.twig
groups/group.twig
```
### Assets (Pro)

If you have a file displayer `image` for a field `topImage` on a layout of type `entry` for a entry type `blog` in a view mode `small`, the asset templates will take this precedence :
```
files/entry/blog/small/image-topImage.twig
files/entry/blog/small/image.twig
files/entry/blog/image-topImage.twig
files/entry/blog/image.twig
files/entry/image-topImage.twig
files/entry/image.twig
files/image-topImage.twig
files/image.twig
```

More templates and variables can be defined by listening to events on the `ViewService` class :

- Layouts : event `BEFORE_RENDERING_LAYOUT`  
- Assets : event `BEFORE_RENDERING_FILE`  
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

Those events can also be used to modify elements's classes and attributes :

```
Event::on(ViewService::class, ViewService::BEFORE_RENDERING_ASSET, function (RenderEvent $event) {
    $e->variables['classes']->add(['my-class', 'my-other-class']);
    $e->variables['attributes']->add('id', 'my-id');
    $e->variables['containerClasses']->add('container-class');
    $e->variables['containerAttributes']->add('id', 'container-id');
    $e->variables['labelClasses']->remove('label-class');
    $e->variables['labelAttributes']->remove('label-id');
});
```

### Theme preferences (Pro)

Each Theme can gain control on the classes and attributes defined for each layout/block/field/file/group/region by defining a [preference class](src/base/ThemePreferences.php).  
To override the preferences for your theme, override the method `getPreferencesModel(): ThemePreferencesInterface` of its main class.

### Debug (Pro)

The available templates and variables can be printed as html comments by enabling the option in the settings.

Shortcuts for layout management can be shown on the frontend by enabling the option in the settings.

### Root templates folder

It is recommended to not use the root `templates` folder when using themes, if some templates are defined both in this folder and in a theme, the root templates folder will take precedence.

A theme **can't** override templates that have a namespace (ie other plugin templates), unless they register their templates roots with the '' (empty string) key.

## Eager loading (Pro)

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

## Reinstall data (Pro)

If something looks off in the displays, you can always reinstall the themes data in the plugin settings. This will create missing fields/displayers for all themes and elements (entry types, category groups etc), and delete the orphans.

## Caching (Pro)

### Rules cache  (Pro)

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

Examples [here](vue/src/blockStrategies/main.js)