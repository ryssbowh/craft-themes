# ryssbowh/craft-themes Changelog

## 3.3.9 - 2022-11-24

### Fixed
- Fixed issue setting dev mode in production mode

## 3.3.8 - 2022-11-23

### Fixed
- Fixed issue registering Twig before it was initialised
- Fixed issue accessing element query before it was initialized

## 3.3.7 - 2022-11-16

### Changed
- Updated documentation urls

## 3.3.6 - 2022-11-15

### Fixed
- Fixed an issue with commerce products shortcuts for non-pro versions
- Fixed error with author displayer not rendering the right view mode

## 3.3.5 - 2022-10-30

### Fixed
- Fixed utilities url for sites in subfolders

## 3.3.4 - 2022-10-12

### Changed
- Changed plugin icons

## 3.3.3 - 2022-10-05

### Fixed
- Fixed issue with layouts for disabled 3rd party plugins

## 3.3.2 - 2022-09-08

### Fixed
- Fixed error when changing a field's type
- Fixed ajax url for overriden templates utility
- Added missing register form block options

## 3.3.1 - 2022-06-22

### Fixed
- Issue when saving users in a console command

## 3.3.0 - 2022-06-18

### Added
- Utility page to check which templates are overridden for each theme
- Field displayer that handle files (assets, user photo etc) must implement `FileFieldDisplayerInterface`

### Fixed
- Removed Themes icon vertical align

### Changed
- The twig macro `renderAsset` signature has changed from `renderAsset(asset, field, options)` to `renderAsset(asset, field)` as the file displayer is now coming from the field displayer instead of the options

## 3.2.4 - 2022-06-12

### Fixed
- Removed folder validation on settings as it messes with saving rules on a fresh install

## 3.2.3 - 2022-06-09

### Fixed
- Fixed issue with blocks elements field creating duplicates

## 3.2.2 - 2022-06-02

### Fixed
- Fixed issue when registering asset on homepage (/)

## 3.2.1 - 2022-05-29

### Fixed
- Fixed neo fields eager loading issue
- Fixed overflow on block options modal

## 3.2.0 - 2022-05-26

### Added
- [Neo](https://plugins.craftcms.com/neo) fields support

### Fixed
- Fixed issue where matrix fields would not be ordered correctly
- Asset render file displayer now the default for UserPhoto fields
- Matrix slick field displayer now not the default for Matrix fields
- Fixed npm vulnerabilities

### Changed
- Assets fields now have their own links displayer

## 3.1.9 - 2022-05-15

### Fixed
- Fixed elements shortcuts twig error

## 3.1.8 - 2022-05-14

### Fixed
- Removed GLOB_BRACE which doesn't exist on some OS
- Added missing exception

## 3.1.7 - 2022-05-14

### Fixed
- Fixed issue in migration

## 3.1.6 - 2022-05-14

### Fixed
- Fixed issue in migration

## 3.1.5 - 2022-05-14

### Fixed
- Fixed issue in migration

## 3.1.4 - 2022-05-11

### Fixed
- Store not picking up version

## 3.1.3 - 2022-04-09

### Fixed
- Fixed an issue with table fields not saving properly
- Fixed an error when deleting a non existing display item

## 3.1.2 - 2022-03-28

### Fixed
- Asset bundles not registered on homepage

## 3.1.1 - 2022-03-24

### Fixed
- Trying to fix changelog

## 3.1.0 - 2022-03-23

### Added
- Integration to commerce
- Fields can now have parents
- Layouts can now have parents
- Allowed 'onLabel' and 'offLabel' on lightswitch Vue component
- Twig tests `is instanceof` and `is numeric`
- [Super Table](https://plugins.craftcms.com/super-table) support
- [Typed link](https://plugins.craftcms.com/typedlinkfield) support
- Nested multi fields (matrix, super table) support
- Layout types now registered through event `LayoutService::EVENT_REGISTER_TYPES`
- Available layouts now registered through event `LayoutService::EVENT_AVAILABLE_LAYOUTS`
- Layout for current request can now be resolved through event `LayoutService::EVENT_RESOLVE_REQUEST_LAYOUT`
- The themes folder can now be changed in settings

### Fixed
- Fixed small modal sizes
- Fixed issue where custom layouts would have displays created for them automatically
- Fixed issues where field layouts would not have their fields populated properly. [10237](https://github.com/craftcms/cms/issues/10237)
- Fixed issues with fields not properly rebuilt
- Fixed issues with missing fields
- Fixed issues when (un)installing other plugins related to themes
- Fixed command `themes/install`

### Changed
- Installing a theme will fail if this plugin is not installed
- Rendered displayers label
- Matrix fields now use parenting system
- Changed how field components are registered on Vue
- Changed how fields are cloned on Vue
- Changed how layout types are defined

### Removed
- Themes installer module
- Matrix service
- `ThemePlugin::hasDataInstalled()` (replaced by `Themes::$plugin->registry->isInstalled($theme)`)
- Removed all `LayoutService::{type}_HANDLE`. Types are now defined through the event `LayoutService::EVENT_REGISTER_TYPES`

## 3.0.3 - 2022-02-24

### Fixed
- proprietary licence in composer.json

## 3.0.2 - 2022-02-21

### Fixed
- changed Craft requirement to ^3.7

## 3.0.1 - 2022-02-15

### Fixed
- Fixed issue when changing edition in plugin store

## 3.0.0 - 2022-02-14

> {warning} Read the breaking changes included in 3.0 in the [wiki](https://github.com/ryssbowh/craft-themes/wiki) before updating

### Added
- Theme list
- Theme plugins can define a preview image
- View port rule
- Scss compiler
- Asset bundle can be registered as regex
- Themes can be set for CP/Console requests
- Pro version
- Layouts (Pro)
- View modes (Pro)
- Regions (Pro)
- Blocks (Pro)
- Field displayers (Pro)
- File displayers (Pro)

### Changed
- Rules have their own section in CP
- Requires Craft 3.7
- Requires php >= 7.3
- Requires php intl extension

### Deprecated
- Twig variable `themesRegistry`
- Twig variable `theme`

## 2.1.6 - 2022-03-28

### Fixed
- Allow regular expressions to register asset bundles

## 2.1.5 - 2021-08-17

### Fixed
- Do not register bundles for cp requests

## 2.1.4 - 2021-07-14

### Fixed
- Method typo

## 2.1.3 - 2021-07-13

### Fixed
- Do not register bundles for console requests

## 2.1.2 - 2021-06-08

### Fixed
- Error in resolving site rules

## 2.1.1 - 2021-05-10

### Fixed
- Twig extension error in control panel

## 2.1.0 - 2021-05-08

### Changed
- Set default theme for non-web requests
- Throw event at all times to allow theme to be changed

## 2.0.1 - 2021-04-27

### Fixed
- Typo in method name

## 2.0.0 - 2021-02-16

> {warning} If you're updating from 1.x please read the breaking changes included in 2.0 in the [docs](https://github.com/ryssbowh/craft-themes/blob/master/README.md)

### Changed
- Themes are now regular plugins

### Removed
- Dependency to ryssbowh/craft-theme-installer-plugin
- ThemeInterface::getName()
- ThemeInterface::getHandle()
- ThemeInterface::getTemplatePath()

### Added
- ThemeInterface::getTemplatesFolder()
- ThemeInterface::isPartial()

## 1.0.6 - 2021-05-01

### Fixed
- Set default theme for non-web requests

## 1.0.5 - 2021-04-28

### Fixed
- Bug for console requests

## 1.0.4 - 2021-04-28

### Fixed
- Don't set theme for console requests

## 1.0.3 - 2021-04-22

### Fixed
- Fixed site templates roots registration made in backend

## 1.0.2 - 2021-02-04

### Fixed
- Error when no rules are defined

## 1.0.1 - 2021-02-01

### Fixed
- Composer 2 compatibility

## 1.0.0 - 2021-01-31

### Added
- Rule system

### Changed
- Theme cache in vendor folder
- Dependency to ryssbowh/craft-theme-installer-plugin
- themes service renamed to registry

## 0.1.0 - 2021-01-27

### Added
- Initial release