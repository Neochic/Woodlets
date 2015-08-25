//make requirejs sync
requirejs.nextTick = function (fn) {
    fn();
};

requirejs(['requirejs-config'], function () {
    requirejs(['bootstrap'], function () {

    });
});