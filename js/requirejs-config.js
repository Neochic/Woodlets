requirejs.config({
    paths: {
        'jquery': '../bower_components/jquery/dist/jquery',
        'jquery-ui.sortable': '../bower_components/jquery-ui/ui/sortable',
        'jquery-ui.core': '../bower_components/jquery-ui/ui/core',
        'jquery-ui.mouse': '../bower_components/jquery-ui/ui/mouse',
        'jquery-ui.widget': '../bower_components/jquery-ui/ui/widget',
        'bluebird': '../bower_components/bluebird/js/browser/bluebird.core',
        'async': '../bower_components/requirejs-plugins/src/async',
        'jquery-locationpicker': '../bower_components/jquery-locationpicker-plugin/dist/locationpicker.jquery',
        'bootstrap-datepicker': '../bower_components/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker',
        'moment': '../bower_components/moment/moment',
        "inputmask": "../bower_components/jquery.inputmask/dist/inputmask/inputmask",
        "inputmask-date-extensions": "../bower_components/jquery.inputmask/dist/inputmask/inputmask.date.extensions",
        "inputmask.dependencyLib": "../bower_components/jquery.inputmask/dist/inputmask/inputmask.dependencyLib",
        "jquery-inputmask": "../bower_components/jquery.inputmask/dist/inputmask/jquery.inputmask",
        "jquery-caret": "../bower_components/jquery.caret/dist/jquery.caret-1.5.0",
        "jquery-ui.slider": "../bower_components/jquery-ui/ui/slider"
    },
    shim: {
        'bootstrap-datepicker': {
            deps: ['jquery-private', 'moment']
        },
        "jquery-caret": {
            deps: ["jquery"]
        },
        "jquery-locationpicker": {
            deps: ["jquery"]
        }
    },
    map: {
        '*': { 'jquery': 'jquery-private' },
        'jquery-private': { 'jquery': 'jquery' },
        'jquery-ui.sortable': {
            'core': 'jquery-ui.core',
            'mouse': 'jquery-ui.mouse',
            'widget': 'jquery-ui.widget'
        },
        'jquery-ui.slider': {
            'core': 'jquery-ui.core',
            'mouse': 'jquery-ui.mouse',
            'widget': 'jquery-ui.widget'
        },
        'jquery-ui.mouse': {
            'widget': 'jquery-ui.widget'
        }
    },
    wrap: true,
    wrapShim: true,
    findNestedDependencies: true
});

//make requirejs sync
requirejs.s.contexts._.nextTick = function (fn) {
    fn();
};
