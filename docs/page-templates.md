# Page templates
The page templates define which columns are available on the pages and which widgets can be added to those columns.  
You can also add additional fields to the page to handle general page content. This fields may inherit content from parent pages.

## Creating a page template
If it's not already there create the directory ```woodlets/pages``` in your theme directory and put a new twig template into it. (If you're not familiar with Twig you should read [the documentation](http://twig.sensiolabs.org/doc/templates.html))  
Add a ```view```- and a ```form```-block to your template.  
Each page template must extend a layout. You can always extend ```layouts/default.twig``` if there is no other layout available.

### Form
The ```form```-block is used to configure the columns and page fields. The context of the block provides a page configuration object as ```woodlets```.

#### Methods of the page configuration object
* ```setTitle(string $title)``` - Sets the title shown in the backend.
* ```addCol(string $id, string $title, object $config)``` - Adds a column to the page template. The ```$config``` object must contain an attribute ```allowed``` with an array of allowed widgets (native WordPress widget IDs or alias of Woodlets widgets) as value.
* ```mainCol(string $id)``` - Defines the column with the ```$id``` as the main column. The content of this column is used to generate the excerpt. If this method is not called for a template the first column is used by default.
* ```section(string $title)``` - Add a section for the additional page fields. This method returns a form configurator object.

#### Methods of the form configuration object
* ```add(string $type, string $name, array $config)``` - Adds a form field to the section. (Check [field types documentation](field-types.md) for available types and configurations.)  
  Additional to the normal field configuration settings the special setting ```inherit``` is available for all field types on the additional page fields. If it's set to true the content of the field can be inherited from the parent page.

### View
The view block should only contain the columns and content of the page. It should not contain any redundant content that is shown on all pages, since this block is used for search. Put the redundant content into the layout or the content block.

*Note: To make search work a rendered version of the view block is copied to the post content on save. Therefore keeping the view block clean also helps to get clean content if you switch Woodlets off for some reason.*

The context contains a helper object available as ```woodlets```.
Check [view helper documentation](view-helper.md) for details.

The most important methods are ```getCol($id)``` and ```getPageConfig()```. The former is used to render the contents of the columns. The latter is used to access additional page fields.

### Example
```twig
{% extends 'layouts/default.twig' %}
{% block form %}
    {{
        woodlets.addCol('main', 'Main Col',{
            'allowed': ['text', 'other-widget']
        })
    }}
    {{
        woodlets.section("Header")
            .add('text', 'teaserText', {
                'label': 'Teaser text',
                'inherit': true
            })
            .add('media', 'headerImage', {
                'label': 'Header image',
                'inherit': true
            })
    }}
    {{
        woodlets.section("Additional configuration")
            .add('select', 'color', {
                'label': 'Color',
                'options': {
                  'red': 'Red',
                  'blue': 'Blue'
                }
            })
    }}
{% endblock %}

{% block content %}
  {% set pageConfig = woodlets.getPageConfig() }
  <div class="{{ pageConfig.color }}">
    <div class="header">
      {{
        woodlets.wp_get_attachment_image(pageConfig.headerImage)|raw
      }}
      <div>{{ pageConfig.teaserText }}</div>
    </div>
    {{ parent() }}
  </div>
{% endblock %}

{% block view %}
    {{ woodlets.getCol('main') }}
{% endblock %}
```

Be sure to set the correct main column and keep the view block clean. You may display columns in the content instead of the view block if they don't contain page specific content.

```twig
{% extends 'layouts/default.twig' %}
{% block form %}
    {{
      woodlets.setTitle('My page template')
        .addCol('left', 'Left Col',{
          'allowed': ['text']
        })
        .addCol('main', 'Main Col',{
          'allowed': ['text', 'other-widget']
        })
        .addCol('right', 'Right Col',{
          'allowed': ['search']
        })
        .mainCol('main')
    }}
{% endblock %}

{% block content %}
  <div>
    {{ parent() }}
    <div class="right">
      {{ woodlets.getCol('right') }}
    </div>
  </div>
{% endblock %}

{% block view %}
  <div class="left">
    {{ woodlets.getCol('left') }}
  </div>
  <div class="main">
    {{ woodlets.getCol('main') }}
  </div>
{% endblock %}
```
