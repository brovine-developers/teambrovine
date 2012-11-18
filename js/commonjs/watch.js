var watch = require('watch');
var exec = require('child_process').exec;

var required = false;

if (process.argv.length < 5 || isNaN(parseInt(process.argv[4]))) {
   console.log('Usage: node watch.js <command:Str> <watchDir:Str> <timeout:Int>');
}
else {
   var command = process.argv[2];
   var dir = process.argv[3];
   var timeout = parseInt(process.argv[4]);

   watch.watchTree(dir, function (f) {
      required = true;
   });

   var refresh = function () {
      if (required) {
         required = false;

         exec(command, function (err, stdout, stderr) {
            if (err || stderr !== '') {
               console.log('ERROR: calling make.');
               console.log('make STDOUT:' + stdout);
               console.log('make STDERR:' + stderr);
               console.log('----');
            }
            else {
               console.log('make:' + stdout);
               console.log('----');
            }
         });
      }
   };

   setInterval(refresh, timeout);
}
