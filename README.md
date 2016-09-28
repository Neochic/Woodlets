# Woodlets [![Build Status](https://travis-ci.org/Neochic/Woodlets.svg)](https://travis-ci.org/Neochic/Woodlets)
**Caution: This plugin is still alpha. There might be breaking changes.  
However the only change that is planned until beta release that may break anything is the restructuring of the default templates.  
They are used to inherit from in your theme templates. Just keep that in mind and check the changes on the default templates you inherit from before you update until beta is released.  
Beta is going to be released very soon.  
Feedback and feature requests are welcome!**

Woodlets is a WordPress plugin that makes theme development more productive and fun.  
The main features are:
* Heavily Twig based theme development
* Create widgets with a single Twig-Template file
* Multi column page layouts
* Custom page template fields with data inheritance
* Simple way to add controls to the theme customizer
* Compatible with native WordPress widgets

## Installation
Installation via composer is recommended. But there is also a bundled version that can be installed manually, since using composer is not that common in the WordPress world.
### Install via composer
You need to to set [installer path](https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md) for ```wordpress-plugin``` type.
```json
{
    "extra": {
        "installer-paths": {
          "vendor/WordPress/wp-content/plugins/{$name}/": [
            "type:wordpress-plugin"
          ]
        }
    }
}
```

Install Woodlets via composer:
```
composer require neochic/woodlets
```

Check ["Woodlets Seed"-Theme composer.json](https://github.com/Neochic/Woodlets-Seed/blob/master/composer.json) for a working example.

### Install manually
1. Go to [releases page](https://github.com/Neochic/Woodlets/releases) and download the latest bundled release.
2. Extract the zip archive to your WordPress plugins directory (usually "wp-content/plugins/").
3. Activate the plugin on the plugins page of your WordPress installation.

## Getting started
1. Be sure the Woodlets plugin is [installed](#installation) and activated.
2. Create a new theme (as the [WordPress documentation says](https://codex.wordpress.org/Theme_Development#Basic_Templates) it should at least contain ```style.css``` and ```index.php```)
3. Put the following lines into your ```index.php```:

    ```php
    <?php
    do_action('neochic_woodlets_render_template');
    ```
    The action ```neochic_woodlets_render_template``` initializes the template rendering.
4. Activate your new theme.
5. Create your first page template with at least one column.
   Learn [how to create your own page templates](docs/page-templates.md).
6. Create your first Woodlets widget.
   Learn [how to create custom widgets](docs/widgets.md).
7. Add your new widget to the allowed widgets configuration of your page template column.
8. Create or edit a page and use your new page layout to test your widget in backend and frontend.

For the creation of new Woodlets themes you may download the ["Woodlets Seed"-Theme](https://github.com/Neochic/Woodlets-Seed) instead of starting from scratch.
It might also be a good idea to take a look at the ["Woodlets Example"-Theme](https://github.com/Neochic/Woodlets-Example).

### Further Reading:
* [Widgets](docs/widgets.md)
* [Field types](docs/field-types.md)
* [Page templates](docs/page-templates.md)
* [Theme customization](docs/theme-customization.md)
* [Layouts](docs/layouts.md)
* [Post and other templates](docs/post-templates.md)
* [Multilanguage](docs/i18n.md)
* [Actions and filters](docs/actions-and-filters.md)
