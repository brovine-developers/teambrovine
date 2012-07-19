// Initialize application containers
var Brovine = Brovine || {};

/**
 * Creates a tokenInput box out of a regular textbox. "elem" should be
 * a jquery selector pointing at one text box.
 */
Brovine.newRegInput = function (elem, func) {
   var that = {};
   var names = [];

   /**
    * Send an ajax call updating the Genes table each time regulation filter
    * item is added or deleted.
    */
   var updateRegFilter = function (elem) {
      var tokens = $(elem).tokenInput("get");
      names = [];

      $.each(tokens, function (idx, item) {
         names.push(item.name);
      });
   }
   
   var fireToken = function (item) { 
      updateRegFilter(elem);
      func();
   };

   $(elem).tokenInput("ajax/getRegHints", {
      onAdd: fireToken,
      onDelete: fireToken,
      theme: 'facebook',
      hintText: 'Start typing a Regulation'
   });

   var getItems = function () {
      return names;
   };

   that.getItems = getItems;

   return that;
};
