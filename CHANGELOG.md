# ryssbowh/craft-themes Changelog

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

> :warning: If you're updating from 1.x please read the breaking changes included in 2.0 in the [docs](https://github.com/ryssbowh/craft-themes/blob/master/README.md)

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