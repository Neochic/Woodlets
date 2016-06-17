requirejs.config({
    paths: {
        'jquery': '../bower_components/jquery/dist/jquery',
        'jquery-ui.sortable': '../bower_components/jquery-ui/ui/sortable',
        'jquery-ui.core': '../bower_components/jquery-ui/ui/core',
        'jquery-ui.mouse': '../bower_components/jquery-ui/ui/mouse',
        'jquery-ui.widget': '../bower_components/jquery-ui/ui/widget',
        'jquery-ui.datepicker': '../bower_components/jquery-ui/ui/datepicker',
        'bluebird': '../bower_components/bluebird/js/browser/bluebird.core',
        'async': '../bower_components/requirejs-plugins/src/async',
        'jquery-locationpicker': '../bower_components/jquery-locationpicker-plugin/src/locationpicker.jquery'
    },
    shim: {
        'jquery-locationpicker': {
            deps: ['jquery', 'async!https://maps.googleapis.com/maps/api/js?libraries=places']
        }
    },
    map: {
        'jquery-ui.sortable': {
            'core': 'jquery-ui.core',
            'mouse': 'jquery-ui.mouse',
            'widget': 'jquery-ui.widget'
        },
        'jquery-ui.mouse': {
            'widget': 'jquery-ui.widget'
        },
        'jquery-ui.datepicker': {
            'core': 'jquery-ui.core'
        }
    },
    wrap: true,
    findNestedDependencies: true
});

//make requirejs sync
requirejs.s.contexts._.nextTick = function (fn) {
    fn();
};
