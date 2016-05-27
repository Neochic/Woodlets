/* globals window */
define([
], function () {
    var modules = window.neochicWoodlets || [];

    window.neochicWoodlets = {
        push: function(module) {
            console.log('b');
            require(module[0], module[1]);
        }
    };

    for (var i = 0; i < modules.length; i++) {
        console.log('a');
        require(modules[i][0], modules[i][1]);
    }
});
