# Developers

## Update from 2.0 breaking changes :

- Main themes plugin class must inherit `Ryssbowh\CraftThemes\base\ThemePlugin`
- Requires PHP 7.3 minimum

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

Create a new plugin, it's main class must extend `Ryssbowh\CraftThemes\base\ThemePlugin`.

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

You can add a custom layout programmatically by doing :

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

Listen to the `EVENT_REGISTER_BLOCK_PROVIDERS` event of the `BlockProvidersService` class :

```
Event::on(BlockProvidersService::class, BlockProvidersService::EVENT_REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProvidersEvent $event) {
    $event->add(new SystemBlockProvider);
});
```

Your block provider class must implements `BlockProviderInterface`. Blocks defined by the providers are defined in their `$_definedBlocks` attribute.

An exception will be thrown if you register a provider which handle is already registered.

### Modifying a provider's blocks

You can modify blocks provided by a provider by responding to an event :

```
Event::on(SystemBlockProvider::class, BlockProviderInterface::EVENT_REGISTER_BLOCKS, function (RegisterBlockProviderBlocks $event) {
    $event->blocks[] = MyBlock::class;
});
```

An exception will be thrown if you register 2 blocks with the same handle within a provider.

### Defining new blocks

New block classes must implements `BlockInterface`, the model `Block` should be used to extend from.  
You can override the `getOptionsModel` method to define more options.

Validating your options and saving them will be handled automatically, as long as you have defined rules in your block options class.

Blocks use [configurable options](#configurable-options).

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

There are 19 types of fields defined by this plugin.

14 custom fields, created by this plugin, which can have their own displayers :

- Author : handles the author of an entry
- File : handles the file of an asset
- TagTitle : handles the title for a tag
- Title : handles the title of an entry/category
- PostDate : handles the postDate attribute for entries
- DateCreated : handles the dateCreated for all elements
- DateUpdated : handles the dateUpdated for all elements
- LastLoginDate : For users only
- UserFirstName : For users only
- UserLastName : For users only
- UserUsername : For users only
- UserPhoto : For users only
- UserEmail : For users only
- ElementUrl : handles the url for entries/categories

And 5 that handle Craft fields, those can't have their own displayers. Their displayers will display the Craft field associated with them :

- CraftField : handles all Craft fields except Matrix and Table
- Matrix : handles Craft matrix fields
- MatrixField : handles the fields within a matrix
- Table : handles Craft table fields
- TableField : handles the fields within a table field. This transform table fields columns in proper fields

A field displayer defines how a field is rendered on the front end, each field displayer will handle one type of field. This is controlled by the method `getFieldTarget(): string` which will return either the class of the Craft field this displayer can handle, or the class of the themes field (like `Title`, `Author` etc).

### Define a new field

Register your php field class (implementing `FieldInterface`) by responding to the event :

```
Event::on(FieldsService::class, FieldsService::EVENT_REGISTER_FIELDS, function (RegisterFieldsEvent $event) {
    $event->register(MyField::class);
});
```
Custom fields (any field that doesn't extend from `CraftField`) can be created automatically on layouts if you return true to the method `shouldExistOnLayout(LayoutInterface $layout)`. By default this method returns false.  
This method won't have any effect for fields that extends `CraftField`, as they will be created automatically (assuming a Craft field exists on the category group/entry type).

If your field is "complex" (has sub fields for example), you may need a bespoke Vue component to render it in CP :

To hook in the backend Vue system, register a js file with a bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::EVENT_REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
Your javascript must respond to the event `register-fields-components` and add your component and clone function to the `event.detail` variable.

Examples [here](vue/src/fields/main.js)

You may also need to modify displayers so that they can handle your field :

```
Event::on(UrlLink::class, FieldDisplayerService::EVENT_REGISTER_FIELD_TARGETS, function (RegisterDisplayerTargetsEvent $event) {
    $event->targets[] = MyField::class;
});
```

## Field displayers (Pro)

### Define a new displayer

Register your displayer by listening to the event :

```
Event::on(FieldDisplayerService::class, FieldDisplayerService::EVENT_REGISTER_DISPLAYERS, function (RegisterFieldDisplayerEvent $event) {
    $event->register(MyFieldDisplayer::class);
});
```

Your field displayer class must implements `FieldDisplayerInterface` class (`FieldDisplayer` model should be used to extend from) and define the field it can handle in its `getFieldTargets` method. 

Displayers use [configurable options](#configurable-options).

## Modify default displayer

Set the default displayer for a field

```
Event::on(Title::class, FieldDisplayerService::EVENT_DEFAULT_DISPLAYER, function (RegisterFieldDefaultDisplayerEvent $event) {
    $event->default = 'displayer-handle';
});
```
Note that this won't have any effect on fields already installed.

### Date/Time displayers

This plugin works with the intl extension for all displayers handling date/time. All formats are expected to be in [icu](https://unicode-org.github.io/icu/userguide/format_parse/datetime/).

When outputing a date value in templates, you can use the `format_datetime` filter :

```
{{ date|format_datetime(pattern=format,locale=craft.app.locale) }}
```

## File displayers (Pro)

A file displayer defines how an asset file is rendered on the front end. Each displayer can handle one or several asset kinds.

### Define a new displayer

Register your displayer by listening to the `EVENT_REGISTER_DISPLAYERS` event of the `FileDisplayerService` class :

```
Event::on(FileDisplayerService::class, FileDisplayerService::EVENT_REGISTER_DISPLAYERS, function (RegisterFileDisplayerEvent $event) {
    $event->register(MyFileDisplayer::class);
});
```

Your file displayer class must extend the `FileDisplayerInterface` class (`FileDisplayer` model should be used to extend from) and define one or several asset kinds in the `getKindTargets` method. The '\*' can be used to indicate this displayer can handle all asset kinds.  

Displayers use [configurable options](#configurable-options).

### Modify kind targets

You can change which kinds a displayer can handle :
```
Event::on(Code::class, FileDisplayerService::EVENT_KIND_TARGETS, function (RegisterDisplayerTargetsEvent $e) {
    $e->targets[] = 'audio';
});
```
And modify default displayers :
```
Event::on(FileDisplayerService::class, FileDisplayerService::EVENT_DEFAULT_DISPLAYERS, function (RegisterFileDefaultDisplayerEvent $e) {
    $e->defaults['audio'] = 'my-displayer-handle';
});
```

## Configurable options

Many classes (field/file displayers, blocks, cache strategies) use configurable options, a class that allow modifications of options and how they are rendered in CP by other modules.  
A configurable options class must define `defineOptions(): array` and `defineDefaultValues(): array`.

These are the allowed field types that must be referenced in the `field` attribute of an option definition, they control how the option is rendered in CP :
- checkboxes
- color
- date
- datetime
- lightswitch
- multiselect
- radio
- select
- text
- textarea
- time
- elements : select an element (user, entry, category or asset) through a modal, and a view mode for each
- fetchviewmode : fetch view modes for a layout type and current theme and an optional element. Displays a select
- filedisplayers : File displayers options for each asset kind

They all have different options, see [here](vue/src/forms)

You can add/remove/change options by responding to the options definitions event :

```
Event::on(AssetLinkOptions::class, AssetLinkOptions::EVENT_OPTIONS_DEFINITIONS, function (DefinableOptionsDefinitions $e)) {
    $e->definitions['newOption'] = [
        'field' => 'text',
        'label' => 'My new option',
        'required' => true,
        'type' => 'number',
        'min' => 10,
        'step' => 1
    ];
    $e->defaultValues['newOption'] = 56;
}
``` 
If you need to define validation rules as well :
```
Event::on(AssetLinkOptions::class, AssetLinkOptions::EVENT_DEFINE_RULES, function (DefineRulesEvent $e)) {
    $e->rules[] = ['newOption', 'required'];
    $e->rules[] = ['newOption', 'double'];
}
```
:warning: Options definitions must not fetch layouts/displays/view modes from database, or the installation may fail.  
:warning: Changing options may break the displayer and its rendering

### New Vue component

If you need to define a new Vue form field component, register an asset bundle :
```
Event::on(CpDisplayController::class, CpDisplayController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
//or
Event::on(CpBlocksController::class, CpBlocksController::REGISTER_ASSET_BUNDLES, function (RegisterBundles $event) {
    $event->bundles[] = MyBundle::class;
});
```  
Your javascript must respond to the event `register-form-fields-components` and add your component `event.detail` variable.
```
import MyFieldComponent from './myFieldComponent';

document.addEventListener("register-form-fields-components", function(e) {
    event.details['my-field'] = MyFieldComponent;
});
```
You can now use `my-field` as a value for the `field` argument of any field definition. Your component will receive these 4 props :
```
value
definition: Object
errors: Array
name: String
```

## Templating (Pro)

Templates are inherited, so if you call a template that isn't defined in your theme but exist in a parent theme, the parent template will be loaded.

Each element of the page (layouts, regions, blocks, field and file displayers) templates can be overriden by your themes using a specific folder structure that allows much granularity.

### Layout keys

Each type of layout defines a key for templating, like so :
- For an entry layout it will be the handle of the section, a hyphen, and the handle of the entry type, example `blog-article`
- For a user : `user`
- For a category group, volume, tag group or global set it will be their handle
- For a custom layout, the handle of the layout

### Layouts

There are two ways to render layouts: regions or displays. The method `Layout::render(Element $element, string $viewMode)` will render the displays, the other `$layout->renderRegions()` the regions.

Rendering the regions will call a special template defined by the theme in `ThemePlugin::getRegionsTemplate()`, by default this equals to `regions`.

You can set any templates to your category groups and sections, if a layout matches for that request, the layout associated will be added to the variables. In your template you simply need to call `{{ layout.renderRegions()|raw }}`. The view mode for such a request is always the default one.

The available templates will take this order (from most to less important) :

```
layouts/{type}/{key}-{viewMode}.twig
layouts/{type}/{key}.twig
layouts/{type}.twig
layouts/layout.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{viewMode}` is the rendered view mode's handle.

### Regions

The available templates will take this order (from most to less important) :
```
regions/{type}/{key}/region-{handle}.twig
regions/{type}/{key}/region.twig
regions/{type}/region-{handle}.twig
regions/{type}/region.twig
regions/region-{handle}.twig
regions/region.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{handle}` is the handle of the region.

### Blocks

The available templates will take this order (from most to less important) :
```
blocks/{type}/{key}/{region}/{handle}.twig
blocks/{type}/{key}/{handle}.twig
blocks/{type}/{handle}.twig
blocks/{handle}.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{region}` is the handle of the region the block is in.  
Where `{handle}` is the machine name of the block. A block `latestBlogs` of a provider `system` will be `system-latestBlogs`

### Fields

The available templates will take this order (from most to less important) :
```
fields/{type}/{key}/{viewMode}/{displayer}-{field}.twig
fields/{type}/{key}/{viewMode}/{displayer}.twig
fields/{type}/{key}/{displayer}-{field}.twig
fields/{type}/{key}/{displayer}.twig
fields/{type}/{displayer}-{field}.twig
fields/{type}/{displayer}.twig
fields/{displayer}-{field}.twig
fields/{displayer}.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{displayer}` is the handle of the field displayer.  
Where `{field}` is the handle of the field.  
Where `{viewMode}` is the rendered view mode's handle.

### Groups

The available templates will take this order (from most to less important) :
```
groups/{type}/{key}/{viewMode}/group-{handle}.twig
groups/{type}/{key}/{viewMode}/group.twig
groups/{type}/{key}/group-{handle}.twig
groups/{type}/{key}/group.twig
groups/{type}/group-{handle}.twig
groups/{type}/group.twig
groups/group-{handle}.twig
groups/group.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{handle}` is the group's handle.  
Where `{viewMode}` is the rendered view mode's handle.

### Files

The available templates will take this order (from most to less important) :
```
files/{type}/{key}/{viewMode}/{displayer}-{field}.twig
files/{type}/{key}/{viewMode}/{displayer}.twig
files/{type}/{key}/{displayer}-{field}.twig
files/{type}/{key}/{displayer}.twig
files/{type}/{displayer}-{field}.twig
files/{type}/{displayer}.twig
files/{displayer}-{field}.twig
files/{displayer}.twig
```
Where `{type}` is the type of layout.  
Where `{key}` is as defined above.  
Where `{displayer}` is the handle of the file displayer.  
Where `{field}` is the handle of the field.  
Where `{viewMode}` is the rendered view mode's handle.

### Events

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
Or to skip the rendering of any element :
```
Event::on(ViewService::class, ViewService::BEFORE_RENDERING_ASSET, function (RenderEvent $event) {
    $event->render = false;
});
```

### Theme preferences (Pro)

Each Theme can define default classes and attributes for each layout/block/field/file/group/region by defining a [preference class](src/base/ThemePreferences.php).  
To override the preferences for your theme, override the method `getPreferencesModel(): ThemePreferencesInterface` of its main class.

### Root templates folder

:warning: It is recommended to not use the root `templates` folder when using themes, if some templates are defined both in this folder and in a theme, the root templates folder will take precedence.

:warning: A theme **can't** override templates that have a namespace (ie other plugin templates), unless they register their templates roots with the '' (empty string) key.

## Eager loading (Pro)

By default, when a layout is rendered it will eager load every field it contains.  
This could be changed by creating the `config/themes.php` file :

```
<?php 

return [
    'eagerLoad' => false
];
```
:warning: All the default templates defined by this plugin expect fields to be eager loaded, if you switch off that feature you need to make sure every template is overriden.

## Twig

Available variables :

`craft.themes.layouts` : Layouts service  
`craft.themes.viewModes` : View mode service  
`craft.themes.registry` : Theme registry  
`craft.themes.view` : Theme view service  
`craft.themes.current` : Current theme

Array test : `{% if variable is array %}`

## Aliases

5 new aliases are set :

`@themePath` : Base directory of the current theme. This is not set if no theme is set.

And 4 that are not used by the system, but could be useful if you're using a tool (such as webpack, gulp etc) to build your assets :

`@themesWebPath` : Web directory for themes, equivalent to `@root/web/themes`  
`@themesWeb` : Web url for themes, equivalent to `@web/themes`  
`@themeWebPath` : Web directory for current theme. This is not set if no theme is set.  
`@themeWeb` : Web url for the current theme. This is not set if no theme is set.

## Reinstall data (Pro)

If something looks off in the displays, you can always reinstall the themes data in the plugin settings. This will create missing fields/displayers for all themes and elements (entry types, category groups etc), and delete the orphans.

## Caching (Pro)

### Rules cache  (Pro)

Theme resolution rules will be cached by default on environments where devMode is disabled. If you change rules and deploy to a production environment, clear the cache : `./craft clear-caches/themes-rules-cache`

It can be overriden by creating the `config/themes.php` file :

```
<?php 

return [
    'rulesCache' => false
];
```

### Template cache

Template resolution will be cached by default on environments where devMode is disabled. If you create new templates and deploy to a production environment, clear the cache : `./craft clear-caches/themes-template-cache`

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

Each block can have a cache strategy that defines how it will be cached. Add new strategies by responding to the event :

```
Event::on(BlockCacheService::class, BlockCacheService::REGISTER_STRATEGIES, function (RegisterBlockCacheStrategies $event) {
    $event->add(new MyCacheStrategy);
});
```
Strategy classes must implement `BlockCacheStrategyInterface` and their options extend `BlockStrategyOptions` which is a [configurable options](#configurable-options) class (but is not modifiable through events).

