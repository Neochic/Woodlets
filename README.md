# Woodlets [![Build Status](https://travis-ci.org/Neochic/Woodlets.svg)](https://travis-ci.org/Neochic/Woodlets)
**Caution: This plugin is alpha. Everything might change at any time. Please do not use in production yet.  
However most of functionality is ready to be tested. Feedback and feature requests are welcome!**

Woodlets is a WordPress plugin that makes theme development more productive and fun.  
The main features are:
* Heavily Twig based theme development
* Create widgets with a single Twig-Template file
* Multi column page layouts
* Custom page template fields with data inheritance
* Simple way to add controls to theme customizer
* Compatible to native WordPress widgets

## Installation
Installation via composer is recommended. But there is also a bundled version that can be installed manually, since using composer is not that common in the WordPress world.
### Install via composer
Woodlets is not yet registered at packagist, therefore it has to be added to "repositories" manually. *(This will change soon.)*  
```json
//composer.json
{
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "neochic/woodlets",
        "type": "wordpress-plugin",
        "version": "0.1.1",
        "dist": {
          "type": "zip",
          "url": "https://github.com/Neochic/Woodlets/releases/download/v0.1.1/woodlets-v0.1.1.zip"
        },
        "require": {
          "twig/twig": "^1.19",
          "pimple/pimple": "^3.0",
          "composer/installers": "^1.0"
        },
        "autoload": {
          "psr-4": {
            "Neochic\\Woodlets\\": "src/"
          }
        }
      }
    }
  ],
  "require": {
    "neochic/woodlets": "~0.1.1"
  }
}
```
Don't forget to replace 0.1.1 with the current release if a more recent one is available. You can find the latest release on [releases page](https://github.com/Neochic/Woodlets/releases).
### Install manually
1. Go to [releases page](https://github.com/Neochic/Woodlets/releases) and download the latest bundled release.
2. Extract the zip archive to your WordPress plugins directory (usually "wp-content/plugins/").
3. Activate the plugin on plugins page of your WordPress installation.

## Getting started
1. Be sure Woodlets plugin is [installed](#installation) and activated.
2. Create a new theme (as [WordPress documentation says](https://codex.wordpress.org/Theme_Development#Basic_Templates) it should at least contain ```style.css``` and ```index.php```)
3. Put the following lines into your ```index.php```:

    ```php
    <?php
    do_action('neochic_woodlets_render_template');
    ```
    The action ```neochic_woodlets_render_template``` initializes the template rendering.
4. Activate your new theme.
5. Edit a page or create and try the Woodlets column editor.
   By default there is only the native WordPress Text-Widget enabled. Continue reading to learn [how to create custom widgets](docs/widgets.md).
6. Check the content you added in the frontend.
   Woodlets provides very basic layout and page templates as base for your own layouts. Learn [how to create your own page templates](docs/page-templates.md).

For the creation of new Woodlets themes you may download the ["Woodlets Seed"-Theme](https://github.com/Neochic/Woodlets-Seed/releases) instead of starting from scratch.
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
