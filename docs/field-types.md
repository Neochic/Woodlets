# Field types
Woodlets provides a set of the most important field types. They should be fine for most of the projects. We're going to add more field types in future.  
However it's planned that you can easily write your own field types or install plugins that provide additional field types in future.

## Available field types
* [Text](#text)
* [Textarea](#textarea)
* [Rich Text Editor](#rich-text-editor)
* [Selectbox](#selectbox)
* [Radio-Select](#radio-select)
* [Checkbox](#checkbox)
* [WordPress Media Selector](#wordpress-media-selector)
* [Content Area](#content-area)
* [Location](#location)
* [Datetime](#datetime)
* [Slider](#slider)

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

*Note: You may use ```neochic_woodlets_rte_settings```-filter to set default settings for your Rich Text Editors. Read more on [actions and filters page](actions-and-filters.md).*

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

*Note: Use the ```contentArea()``` method of the [view helper](view-helper.md) to display the widgets in the frontend.*

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

### Location
The Location field-type provides a convenient user interface for saving location data. Since it's based on GoogleMaps, as a prerequisite its mandatory to configure a [Google Maps API Key](https://developers.google.com/maps/documentation/javascript/get-api-key) within your ```wp-config.php```, which will be used to retreive the API inside the backend.

```php
define('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY');
```

_Not having set a proper API key will break the functionality of location-type fields ._

#### Configuration
* ```label``` - The label text for the form control.
* ```default``` - Location Object to be used as default map center, when no location has been selected.
    * ```lat``` - The locations latitude
    * ```lat``` - The locations longitude

#### Example
```twig
{{
  woodlets.add('location', 'locationFieldName', {
    'label': 'My location',
    'default': {
        'lat': 48.772292,
        'lng': 9.168389
    }
  })
}}
```

### Datetime
The Datetime field-type provides inputs for datetime, date and time data. It can be configured to only act as a fallback for given inputs, or to enforce the custom UI regardless of the availability of native inputs within the current browser. There's also the possibility to link two inputs together to act as a date-range input.

#### Configuration
* ```label``` - The label text for the form control.
* ```withtime``` default: ```true``` - Should the field incorporate a timepicker?
* ```withdate``` default: ```true``` - Should the field incorporate a datepicker?
* ```disableNative``` default: ```true``` - Should the custom input be enforced or just be used as a fallback if there's no native type.
* ```endswith``` - Set to the field name of another equivalent datetime input in the same form, to have them act together as a date-range.
* ```format``` - Object containing format information separated by language. The display format will be chosen based on the backend language selected by the current user. As of now, only English and German display formats have been predefined. The format configuration for a single language consists of three subparts for the different input configurations:
    * ```datetime-local``` - will be used when both, date and time are enabled
    * ```date``` - will be used when time input is disabled
    * ```time``` - will be used when date input is disabled

    each of the above again has to contain the following three keys:

    * ```save``` - the format being used for saving the date to the database, according to [MomentJS Format](http://momentjs.com/docs/#/displaying/format/)
    * ```display``` - the display format, according to [MomentJS Format](http://momentjs.com/docs/#/displaying/format/)
    * ```inputmask``` - an extra format string, which has to match the display format, to be used with the [Inputmask Library](https://github.com/RobinHerbots/jquery.inputmask). Instead of adding a completely new inputmask-format you'll probably want to use one of [these](https://github.com/RobinHerbots/jquery.inputmask/blob/3.x/README_date.md).

    study the example below, containing the predefined languages English and German, to get an idea.


#### Example
```twig
{{
  woodlets.add('datetime', 'event_start', {
    'label': 'Start',
    'withtime': true,
    'withdate': true,
    'disableNative': true,
    'endswith': 'event_end',
    'en': {
            'datetime-local': {
                'save': 'YYYY[-]MM[-]DD[T]HH[:]mm',
                'display': 'MM[/]DD[/]YYYY hh:mm A',
                'inputmask': 'mm/dd/yyyy hh:mm xm'
            },
            'date': {
                'save': 'YYYY[-]MM[-]DD',
                'display': 'MM[.]DD[.]YYYY',
                'inputmask': 'mm/dd/yyyy'
            },
            'time': {
                'save': 'HH[:]mm',
                'display': 'hh[:]mm A',
                'inputmask': 'hh:mm t'
            )
        },
        'de': {
            'datetime-local': {
                'save': 'YYYY[-]MM[-]DD[T]HH[:]mm',
                'display': 'DD[.]MM[.]YYYY[ - ]HH:mm',
                'inputmask': 'custom01'
            },
            'date': {
                'save': 'YYYY[-]MM[-]DD',
                'display': 'DD[.]MM[.]YYYY',
                'inputmask': 'dd.mm.yyyy'
            },
            'time': {
                'save': 'HH[:]mm',
                'display': 'HH[:]mm',
                'inputmask': 'hh:mm'
            }
        }
  })

  .add('datetime', 'event_end', {
        'label': 'Ende'
  })

}}
```

### Slider
The Slider field-type provides a slider input to be used for number type fields having a minimal and maximal value.

#### Configuration
* ```min``` default: ```0``` - The minimum value of the slider.
* ```max``` default: ```100``` - The maximum value of the slider.
* ```step``` default: ```1``` - Determines the size or amount of each interval or step the slider takes between the min and max. The full specified value range of the slider (max - min) should be evenly divisible by the step.

#### Example
```twig
{{
  woodlets.add('slider', 'zoom', {
      "label": "Zoom level",
      "min": 1,
      "max": 20,
      "step": 1
  })
}}
```