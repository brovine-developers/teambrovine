$(document).ready(function() {
   if ($.browser.mozilla && $.browser.version.slice(0, 3) <= 1.9) {
      alert ("FireFox < 4 Not supported.");
   }
});
