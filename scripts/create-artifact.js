var fs = require('fs');
var archiver = require('archiver');

var tag = process.env.TRAVIS_TAG;
var name = 'woodlets-'+(tag ? tag : 'latest');

function createArchive(name) {
    var archive = archiver('zip');
    var output = fs.createWriteStream(name + '.zip');

    output.on('close', function () {
        console.log(archive.pointer() + ' total bytes');
    });

    archive.on('error', function (err) {
        throw err;
    });

    archive.pipe(output);

    return archive;
}

var archive = createArchive(name);
var bundle = createArchive(name+'-bundled');

var files = [
    'css/main.css',
    'js/main-build.js',
    'src/**/*',
    'views/**/*',
    'languages/**/*',
    'LICENSE',
    'README.md',
    'woodlets.php'
];

archive.bulk([
    { src: ['composer.json'].concat(files), dest: 'woodlets' }
]);

bundle.bulk([
    { src: ['vendor/**/*'].concat(files), dest: 'woodlets' }
]);


archive.finalize();
bundle.finalize();
