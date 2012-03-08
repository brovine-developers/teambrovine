$(document).ready(function() {
   console.log($.browser);
   if ($.browser.mozilla && $.browser.version.slice(0, 3) <= 1.9) {
      alert ("FireFox < 4 Not supported.");
   }
});
