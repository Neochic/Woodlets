# Woodlets [![Build Status](https://travis-ci.org/Neochic/Woodlets.svg)](https://travis-ci.org/Neochic/Woodlets)
**Caution: This plugin is pre-alpha. Everything might change at any time. Please do not use in production yet.  
However most of functionality is ready to be tested. Feedback and feature requests are welcome!**

Woodlets is a WordPress plugin that makes theme development more productive and fun. The main features are:
* Create widgets with a single Twig-Template file
* Backend editor for pages that uses widgets as page content elements in place of the WordPress WYSIWYG-Editor
* Create Twig based themes with multi column page layouts

## Installation
Installation via composer is recommended. But there is also a bundled version that can be installed manually, since using composer is not that common in the WordPress world.
### Install via composer
Woodlets is not yet registered at packagist, therefore it has to be added to "repositories" manually. *(This will change soon.)*  
```javascript
//composer.json
{
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "neochic/woodlets",
        "type": "wordpress-plugin",
        "version": "0.0.4",
        "dist": {
          "type": "zip",
          "url": "https://github.com/Neochic/Woodlets/releases/download/v0.0.4/woodlets-v0.0.4.zip"
        },
        "require": {
          "composer/installers": "^1.0"
        }
      }
    }
  ],
  "require": {
    "neochic\\woodlets": "~0.0.4"
  }
}
```
Don't forget to replace 0.0.4 with the current release if a more recent one is available. You can find the latest release on [releases page](https://github.com/Neochic/Woodlets/releases).
### Install manually
1. Go to [releases page](https://github.com/Neochic/Woodlets/releases) and download the latest bundled release.
2. Extract the zip archive to your WordPress plugins directory (usually "wp-content/plugins/").
3. Activate the plugin on plugins page of your WordPress installation.