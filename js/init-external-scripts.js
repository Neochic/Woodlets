/* globals window */
define([
], function () {
    var modules = window.neochicWoodlets || [];

    window.neochicWoodlets = {
        push: function(module) {
            require(module[0], module[1]);
        }
    };

    for (var i = 0; i < modules.length; i++) {
        require(modules[i][0], modules[i][1]);
    }
});
