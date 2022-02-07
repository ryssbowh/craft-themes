# Craft themes (v3.x)

Version 3 has brought the pro version which is a big change from the v2 version. You can always use the v3 lite version which is very similar to v2 (make sure you check the breaking changes in the [developers readme](https://github.com/ryssbowh/craft-themes/wiki/Developers#breaking-changes)) with a few other changes.

The pro version aims at taking control over how pages are displayed using an interface in the backend and a theme engine that is reusable, adaptable and easy to use.  
Themes are regular Craft plugins, as such they can be shared on the store, installed from packagist or created by yourself. They can have settings and migrations and most importantly extend each other.  
Craft backend is amazing but if you're not a developer making a frontend can be a bit daunting, the pro version should alleviate this by doing a lot of the work for you.

The lite version will allow you to :
- Install themes from the store or any git repository (composer, github, local filesystem etc)
- Define your own themes that can extend each other
- Choose which theme will be used for which site, language, viewport or url path according to a set of rules.
- Compile scss files

The Pro version will allow you to :
- Define regions in your themes
- Assign blocks to the themes regions
- Define your own blocks
- Define your own view modes for each entry types/category groups/global sets/tag groups/volumes/users layouts
- Choose how your fields and assets are displayed on the front end depending on their view modes and options
- Define your own fields and assets displayers
- Use a templating cascading system that allows bespoke rendering
- Eager load fields automatically
- Use several caching layers for faster rendering

What it doesn't allow you to do :
- Change the backend look and feel
- Override plugins templates (unless specific case, see [developers readme](https://github.com/ryssbowh/craft-themes/wiki/Developers#root-templates-folder))

Consult the [documentation](https://github.com/ryssbowh/craft-themes/wiki) for further details on how to use those features.

## Requirements

Craft 3.7.*  
PHP 7.3 or over
PHP Intl extension

## Testing

This plugin is unit tested with mysql 5.7, postgresql 12.8, Craft 3.7 and php 7.3, 7.4 and 8.0.

## Documentation

- Please consult the [wiki](https://github.com/ryssbowh/craft-themes/wiki)
- Or the [developers wiki](https://github.com/ryssbowh/craft-themes/wiki/developers)
- [Class reference](https://ryssbowh.github.io/craft-themes/namespaces/ryssbowh-craftthemes.html) (from 3.0.0 only)
- 1.x documentation [there](README1.md)
- 2.x documentation [there](README2.md)

## Roadmap/Ideas

- Integrate to Commerce
- Themes preferences not related to project config
- Restricted version of blocks page for envs where admin changes are disabled
- Improve displayer cache by not saving the ones that didn't change
- Add a Theme tab to the debug bar