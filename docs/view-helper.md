# View helper

The view helper object is available as ```woodlets``` in all twig templates, but the form blocks. In the form blocks the view helper is replaced by the form configuration object.

## Methods of the view helper
* ```getPosts()``` - This is an alternative to loop of WordPress to get a more convenient way to access the posts. It just returns an array of posts to iterate over. However you can still use the normal loop syntax of WordPress if you prefer.
* ```getSidebar($id)``` - Displays the sidebar with the ```$id```. It's a wrapper for ```dynamic_sidebar()``` that checks ```is_active_sidebar()``` automatically.
* ```getCol($id)``` - Displays the column with the ```$id```.
* ```getPageConfig()``` - Returns the additional page fields.
* ```contentArea($config)``` - Displays the content area. Expects the value of the content area field type as parameter.
* ```isDebug()``` - Returns if the debug mode ([WP_DEBUG](https://codex.wordpress.org/WP_DEBUG)) is enabled.

## Call global PHP functions
Since WordPress heavily relies on global PHP functions to access or display data the view helper can be used to access any global PHP function.

#### Example
```twig
{{{ woodlets.the_content() }}}
```
