# Field types
Woodlets provides a set of the most important field types. They should be fine for most of the projects. We're going to add more field types in future.  
However it's planned that you can easily write your own field types or install plugins that provide additional field types in future.

## Available field types
### Text
The Text type adds a one-line plain-text input element.

#### Configuration
* ```label``` - The label text for the form control.

#### Example

```twig
{{
  woodlets.add('text', 'headline', {
    'label': 'Headline'
  })
}}
```

### Textarea
The Textarea type adds a multi-line plain-text input element.

#### Configuration
* ```label``` - The label text for the form control.

#### Example

```twig
{{
  woodlets.add('textarea', 'intro', {
    'label': 'Introduction'
  })
}}
```

### Rich Text Editor
The Rich Text Editor type adds a form control that shows a form control with the WordPress bundled TinyMCE.

*Note: You may use ```neochic_woodlets_rte_settings```-filter to set default settings for your Rich Text Editors. Read more on [actions and filters page](docs/actions-and-filters.md).*

#### Configuration
* ```label``` - The label text for the form control.
* ```buttons``` - Shortcut for ```toolbar1``` of ```tinymce```-configuration (gets overwritten by ```toolbar1``` of ```tinymce``` if it's set)
* ```tinymce``` - [TinyMCE configuration object](http://archive.tinymce.com/wiki.php/Configuration)

#### Example
```twig
{{
  woodlets.add('rte', 'body', {
    'label': 'Content',
    'buttons': 'italic, bold, underline, link',
    'tinymce': {
        'autoresize_min_height': 200
    }
  })
}}
```
### Selectbox
The Selectbox type adds a selectbox form control with single item selection.

#### Configuration
* ```label``` - The label text for the form control.
* ```options``` - A key-value object with the options. Keys are used as item value and value is used as item label.  

#### Example
```twig
{{
  woodlets.add('select', 'color', {
    'label': 'Color',
    'options': {
      'red': 'Red',
      'blue': 'Blue'
    }
  })
}}
```

### Radio-Select
The Radio-Select type adds a radio input selection form element.

#### Configuration
* ```label``` - The label text for the form control.
* ```options``` - A key-value object with the options. Keys are used as item value and value is used as item label.  

#### Example
```twig
{{
  woodlets.add('radio', 'icon', {
    'label': 'Icon',
    'options': {
      'coffee': 'Coffee',
      'microphone': 'Microphone',
      'tags': 'Tags'
    }
  })
}}
```

### Checkbox
The Checkbox type adds a checkbox input element.

#### Configuration
* ```label``` - The label text for the form control.

#### Example
```twig
{{
  woodlets.add('checkbox', 'hidden', {
    'label': 'Hidden'
  })
}}
```

### WordPress Media Selector
The WordPress Media Selector type adds a form control to select items from WordPress Media Library ([wp.media)(https://codex.wordpress.org/Javascript_Reference/wp.media)).

*Note: Only frame 'select' and single selection is supported right now. It works fine for selecting files (e.g. for downloads) or images.*  
*However it should already fit most use cases. For example a video widget could be build with a separate media selector for each format. And additional information like a checkbox if autoplay attribute should be set.*

#### Configuration
* ```label``` - The label text for the form control.
* ```type``` - Shortcut for ```library[type]``` in ```wpMedia``` (gets overwritten if also set in ```wpMedia```)
* ```wpMedia``` - [wp.media attributes object](https://codex.wordpress.org/Javascript_Reference/wp.media)

#### Example
```twig
{{
  woodlets.add('media', 'headerImage', {
    'label': 'Image',
    'type': 'image/jpg',
    'wpMedia': {
      'button': {
        'text': 'Add header image'
      }
    }
  })
}}
```

### Content Area
The Content Area type is the most powerful type in Woodlets so far. It allows you to add a list of widgets into your widget. It works pretty much the same as the columns in the [page templates](page-templates.md).

#### Configuration
* ```label``` - The label text for the form control.
* ```allowed``` - Array of allowed widgets (native WordPress widget IDs or alias of Woodlets widgets)

#### Example
```twig
{{
  woodlets.add('contentArea', 'subelements', {
    'label': 'Widget area',
    'allowed': ['text', 'some-other-widget']
  })
}}
```
