# ryssbowh/craft-themes Changelog

## 4.2.9 - 2022-11-11

### Fixed
- Fixed an issue with commerce products shortcuts for non-pro versions

## 4.2.8 - 2022-11-09

### Fixed
- Fixed padding on children layouts
- Fixed errors with Super Table

## 4.2.7 - 2022-10-30

### Fixed
- Fixed utilities url for sites in subfolders

## 4.2.6 - 2022-10-12

### Changed
- Changed plugin icons

## 4.2.5 - 2022-10-05

### Fixed
- Fixed issue with layouts for disabled 3rd party plugins

## 4.2.4 - 2022-09-08

### Fixed
- Fixed error when changing a field's type
- Fixed ajax url for overriden templates utility
- Added missing register form block options
- Fixed potential issue with block cache strategies

## 4.2.3 - 2022-08-08

### Fixed
- Fixed display tabs for Craft > 4.2

## 4.2.2 - 2022-08-08

### Fixed
- Issue where element shortcuts would show for lite versions

## 4.2.1 - 2022-06-22

### Fixed
- Issue when saving users in a console command

## 4.2.0 - 2022-06-18

### Added
- Utility page to check which templates are overridden for each theme
- Field displayer that handle files (assets, user photo etc) must implement `FileFieldDisplayerInterface`

### Fixed
- Removed Themes icon vertical align

## 4.1.7 - 2022-06-12

### Fixed
- Fixed `ReinstallLayoutsJob::execute()` typing

## 4.1.6 - 2022-06-12

### Fixed
- Fixed `ReinstallLayoutsJob::getDescription()` typing
- Removed folder validation on settings as it messes with saving rules on a fresh install

## 4.1.5 - 2022-06-09

### Fixed
- Fixed issue with blocks elements field creating duplicates
- Fixed issue where Vue's bundle names where the same for production and legacy

## 4.1.4 - 2022-06-09

### Fixed
- Reinstated cp shortcuts for globals, users and products. [#11224](https://github.com/craftcms/cms/issues/11224)

## 4.1.3 - 2022-06-08

### Fixed
- Fixed issue with view modes tabs
- Fixed issue with Vue sidebars
- Reinstated cp shortcuts for categories, entries and assets. [#11224](https://github.com/craftcms/cms/issues/11224)

## 4.1.2 - 2022-06-02

### Fixed
- Fixed issue when registering asset on homepage (/)

## 4.1.1 - 2022-05-29

### Fixed
- Fixed overflow on block options modal

## 4.1.0 - 2022-05-27

### Added
- [Neo](https://plugins.craftcms.com/neo) fields support

### Fixed
- Fixed issue where matrix fields would not be ordered correctly
- Asset render file displayer now the default for UserPhoto fields
- Matrix slick field displayer now not the default for Matrix fields
- Fixed npm vulnerabilities

### Changed
- Assets fields now have their own links displayer

## 4.0.7 - 2022-05-15

### Changed
- Removed ecommerce product cp hook

## 4.0.6 - 2022-05-15

### Changed
- Removed elements shortcuts as hooks not defined in Craft 4 anymore

### Fixed
- Fixed permissions issue

## 4.0.5 - 2022-05-14

### Fixed
- Removed GLOB_BRACE which doesn't exist on some OS
- Added missing exception

## 4.0.4 - 2022-05-14

### Fixed
- Fixed issue in migration

## 4.0.3 - 2022-05-14

### Fixed
- Fixed issue in migration

## 4.0.1 - 2022-05-11

### Changed
- removed php requirement

### Added
- Money fields support

## 4.0.0 - 2022-05-10

### Changed
- Craft 4 support