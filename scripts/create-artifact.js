var fs = require('fs');

var archiver = require('archiver');
var archive = archiver('zip');

var tag = process.env.TRAVIS_TAG;
var name = 'woodlets-'+(tag ? tag : 'latest');
var output = fs.createWriteStream(name+'.zip');

output.on('close', function() {
    console.log(archive.pointer() + ' total bytes');
});

archive.on('error', function(err) {
    throw err;
});

archive.pipe(output);

archive.bulk([
    { src: [
        'css/main.css',
        'js/main-build.js',
        'src/**/*',
        'views/**/*',
        'vendor/**/*',
        'composer.json',
        'LICENSE',
        'README.md',
        'woodlets.php'
    ], dest: name }
]);

archive.finalize();