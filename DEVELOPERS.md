# Developers documentation (v3.x)

## Table of contents

- [Update from 2.0 breaking changes](#update-from-20-breaking-changes-)
- [Themes](#themes)
- [Layouts (Pro)](#layouts-pro)
- [Blocks (Pro)](#blocks-pro)
- [Field displayers (Pro)](#field-displayers-pro)
- [File displayers (Pro)](#file-displayers-pro)
- [Configurable options](#configurable-options)
- [Templating (Pro)](#templating-pro)
- [Eager loading (Pro)](#eager-loading-pro)
- [Aliases](#aliases)
- [Caching](#caching)
- [Scss compiling](#scss-compiling)

## Update from 2.0 breaking changes :

- Main themes plugin class must inherit `Ryssbowh\CraftThemes\base\ThemePlugin`
- Requires Craft 3.7 (3.5 and 3.6 support is dropped due to issues that won't be fixed on those versions)
- Requires PHP 7.3 or higher
- Requires PHP Intl extension

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

When a view mode is rendered the eager load map will be built to eager load every possible field on that view mode. The map will also contain nested view modes fields (displayers that render other layout/view mode) and assets transforms. This map will be stored in cache.  

The cache is enabled when devMode is off and can be cleared with the following command : `./craft invalidate-tags/themes::eagerLoad`, it will be automatically cleared for the relevant view modes when anything is changed in them.

Eager loading will nest until 5 levels, after that it will stop.  
Example :
- View mode 'default' :
    - field entries pointing to view mode 'small' : level 1
- View mode 'small'
    - Field categories pointing to view mode 'featured' : Level 2
- View mode 'featured' :
    - Field assets : Level 3

Settings can be controlled by creating the `config/themes.php` file :

```
<?php 

return [
    'eagerLoad' => false,
    'eagerLoadingCache' => false,
    'maxEagerLoadLevel' => 10
];
```
:warning: All the templates defined by this plugin expect fields to be eager loaded, switching off that feature could result in lots of extra n+1 queries (especially if your displayer cache is off).

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

## Caching

### Displayer cache (Pro)

Displayers are cached with the service `DisplayerCacheService`, .  
The caching parameters are controlled by the content block caching strategy. That strategy can define duration and different caching keys depending on user authentication, url and others.

Cache is enabled by default when devMode is disabled, this be changed by creating the `config/themes.php` file :

```
<?php 

return [
    'displayerCache' => false
];
```

Displayer caching use Craft internal caching dependencies, saving an entry for example will clear all the displayer caches that use this entry.

If something changes in your code (templates, theme preferences class, render events) and you're pushing to a production environment, clear the caches : `./craft invalidate-tags/themes::displayers`.

Field displayers are cached in the template `fields/_field` with the token `{% fielddisplayercache %}`, if you override a field template that does not extend this template, you would need to add that token or caching will be skipped.  
Same idea for file displayers which use the token `{% filedisplayercache %}`.

Each displayer method `beforeRender()` will be called, even for cached displayers, that can be used if you need to register asset bundles or other things.

The cache can be disabled at field or displayer level by overriding the `FieldInterface::getCanBeCached(): bool` method. Cache is currently disabled on :
- MatrixField : Cache happens at the Matrix level
- TableField : Cache happens at the Table level
- FileFile and AssetRenderFile : Cache happens at file displayer level

Generally, any displayer that handle Assets (file displayers) should have its cache disabled, it's the file displayer themselves who should be responsible for caching. Caching such a field displayer will result in the `beforeRender` of file displayers to not be called.

### Rules cache  (Pro)

Theme resolution rules will be cached by default on environments where devMode is disabled, this can be changed by creating the `config/themes.php` file :

```
<?php 

return [
    'rulesCache' => false
];
```
If you change rules and deploy to a production environment, clear the cache : `./craft invalidate-tags/themes::rules`.

### Template cache

Template resolution will be cached by default on environments where devMode is disabled, this can be changed by creating the `config/themes.php` file :

```
<?php 

return [
    'templateCache' => false
];
```
If you create new templates and deploy to a production environment, clear the cache : `./craft invalidate-tags/themes::templates`

### Block cache

Block cache will be enabled by default on environments where devMode is disabled, this can be changed by creating the `config/themes.php` file :

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

Each block can disable caching entirely by overriding the method `getCanBeCached(): bool`. The content block is non cacheable, but still has a caching strategy which will define displayers caching.

Block cache can be clear with the following command : `./craft invalidate-tags/themes::blocks`

## Scss compiling

:warning: All scss features are considered unstable until the underlying compiler has a stable release.

Your themes can, if you need to, compile scss files into css. The compiler itself has its own [package](https://github.com/ryssbowh/scss-php-compiler), please refer to it for documentation.

Each of your theme can override its `getScssCompiler(): Compiler` method which must return an instance of `Ryssbowh\ScssPhp\Compiler`, that's where you'd define your compiler plugins, aliases, import paths etc.  
The default compiler defines the following :
- minified css on production
- sourcemaps disabled on production
- parent themes added as import paths. This means you can import scss files from a parent theme.
- Theme file loader plugin for images and fonts. This means you can reference assets (in the `url()` function) from a parent theme and they will be extracted.
- JSON Manifest plugin
- Public path set to `@themesWebPath/theme-handle` which is equivalent to `@webroot/themes/theme-handle`. That's where the css files will be written

Compiling can be done in two ways :

### Scss bundle

Define a bundle that extends `Ryssbowh\CraftThemes\scss\ScssAssetBundle` and reference some scss files to compile. The compiling by default will only happen if devMode is on (This can be changed in the bundle). If you're on devMode and use such a bundle, the scss will be compiled at each request.

Example for a compilation that uses a manifest :
```
use Ryssbowh\CraftThemes\scss\ScssAssetBundle;

class FrontCssAssets extends ScssAssetBundle
{
    public $baseUrl = 'themes/my-theme';

    public $theme = 'my-theme';

    public $scssFiles = [
        'assets/src/scss/app.scss' => 'app.css'
    ];

    public $basePath = '@themeWebPath';

    /**
     * This is only needed if you use a manifest
     */
    public function registerAssetFiles($view)
    {
        $manifestFile = \Craft::getAlias('@themeWebPath/manifest.json');
        if (file_exists($manifestFile)) {
            $manifest = json_decode(file_get_contents($manifestFile), true);
            $this->css[] = $manifest['app.css'];
        }
        parent::registerAssetFiles($view);
    }

    /**
     * Enable/disable compilation here
     */
    protected function isCompilingEnabled(): bool
    {
        return parent::isCompilingEnabled();
    }

    /**
     * Optionally change the compiler, the default one will be used otherwise
     */
    protected function getCompiler(): Compiler
    {
        return parent::getCompiler();
    }
}
```

### In templates

### Raw scss

Use the twig tag `scss` to compile scss at page load.

Example :
```
{% scss %}
    @import "../../scss/main.scss";
    
    h1 a::after { 
      content: "";
      background: center/contain no-repeat url(../../assets/images/favicon.png);
    }
{% endscss %}
```
The scss will be compiled once (and every time the scss is changed) and put in cache so your page load is not slowed down.

Imports or urls path are relative to the folder where the template is.

Caches can be cleared using the backend or the command `craft clear-caches/themes-scss-cache`

### File

You can also compile an existing file :

```
{% scss file "../../assets/src/scss/components/main.scss" %}
```

### Options

Use the `force` option to force the compiling.

```
{% scss force %}
    @import "../../scss/main.scss";
    
    h1 a::after { 
      content: "";
      background: center/contain no-repeat url(../../assets/images/favicon.png);
    }
{% endscss %}

// Or

{% scss file "../../assets/src/scss/components/main.scss" force %}
```

Use the `with options {}` to pass options to the compiler :

```
{% scss with options {style: 'expanded'} %}
    @import "../../scss/main.scss";
    
    h1 a::after { 
      content: "";
      background: center/contain no-repeat url(../../assets/images/favicon.png);
    }
{% endscss %}

// Or

{% scss file "../../assets/src/scss/components/main.scss" with options {style: 'expanded'} %}
```

Note that the options `publicFolder` and `fileName` will have no effect as they will be overridden.