# Woodlets widgets
Widgets are the most important part of Woodlets. They are used as containers for all the content.

## Creating a widget
If it's not already there create the directory ```woodlets/widgets``` in your theme directory and put a new twig template into it. (If you're not familiar with Twig you should read [the documentation](http://twig.sensiolabs.org/doc/templates.html))  
Add a ```view```- and a ```form```-block to your template.

### Form
The ```form```-block is used to configure your widget. The context of the block provides a widget configuration object as ```woodlets```.

#### Methods of the widget configuration object
* ```setTitle(string $title)``` - Sets the title shown in the backend.
* ```setDescription(string $description)``` - Sets the description for the widget.
* ```setAlias(string $alias)``` - Set a unique name that is used to add the widget to a column in the page template.
* ```register()``` - If this method is called the widget is also available for WordPress sidebars.
* ```add(string $type, string $name, array $config)``` - Adds a form field to the widget. (Check [field types documentation](field-types.md) for available types and configurations.)

All methods return the widget configuration object what makes them chainable.

### View
This block is used to render the widget in the frontend. The context contains the contents of the form fields with the configured names as keys.  
There is also a helper object available as ```woodlets```.
Check [view helper documentation](view-helper.md) for details.

### Preview
The ```preview```-block is optional, but it's a good idea to add it to your widget for a better backend experience.  
It works like the ```view```-block, but is used to preview the users content in the column editor.

## Example
```twig
{# woodlets/widgets/my-widget.twig #}
{% block form %}
    {{
        woodlets.setTitle('My first Woodlets widget')
            .setAlias('my-widget')
            .setDescription('An example widget with all field types.')
            .register()
    }}

    {{
        woodlets
            .add('text', 'headline', {
              'label': 'Headline'
            })
            .add('textarea', 'intro', {
                'label': 'Introduction'
            })
            .add('rte', 'body', {
                'label': 'Content',
                'buttons': 'italic, bold, underline, link'
            })
            .add('contentArea', 'subelements', {
                'label': 'Widget area',
                'allowed': ['my-widget']
            })
            .add('select', 'color', {
                'label': 'Color',
                'options': {
                  'red': 'Red',
                  'blue': 'Blue'
                }
            })
            .add('radio', 'icon', {
                'label': 'Icon',
                'options': {
                  'coffee': 'Coffee',
                  'microphone': 'Microphone',
                  'tags': 'Tags'
                }
            })
            .add('media', 'image', {
                'label': 'Image'
            })
            .add('checkbox', 'hidden', {
                'label': 'Hidden'
            })
    }}
{% endblock %}

{% block preview %}
    <h4>{{ headline }}</h4>
    <p>{{ intro }}</p>
    {{ body|raw }}
{% endblock %}

{% block view %}
    {% if not hidden %}
      <div class="my-widget {{ color }} {{ icon }}">
          <h2>{{ headline }}</h2>
          <p>{{ intro }}</p>
          {{ body|raw }}
          {{ woodlets.wp_get_attachment_image(image, [500, 500])|raw }}
          {{ woodlets.contentArea(subelements) }}
      </div>
    {% endif %}
{% endblock %}
```

*Note: Your new widget needs to be enabled for a column or widget area to be available in the backend. Lern [how to create page templates](page-templates.md) and enable widgets in their columns.*
