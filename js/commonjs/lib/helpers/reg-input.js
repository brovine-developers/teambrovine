/**
 * helpers/reg-input.js
 *
 * Therin Irwin
 *
 * Creates a regulation-style tokeninput box out of a regular textbox. "elem"
 * should be a jquery selector pointing at one text box.
 */

var RegFilter = function (elem, table, col, func) {
   if (!(this instanceof RegFilter))
      return new RegFilter(elem, table, col, func);

   var that = this;
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
      table.fnDraw();
      if (func) func();
   };

   $(elem).tokenInput("ajax/getRegHints", {
      onAdd: fireToken,
      onDelete: fireToken,
      theme: 'facebook',
      preventDuplicates: true,
      hintText: 'Start typing a Regulation'
   });

   var getItems = function () {
      return names;
   };

   this.getItems = getItems;

   var filter = function (oSettings, aData, iDataIndex) {
      if (oSettings.sTableId != table.attr('id')) {
         return true;
      }

      if (that.getItems().length == 0) {
         return true;
      }

      for (i = 0; i < that.getItems().length; i++) {
         var item = that.getItems()[i];

         if (item == aData[col]) {
            return true;
         }
      }

      return false;
   };

   $.fn.dataTableExt.afnFiltering.push(filter);

   return this;
};

module.exports = {
   "RegFilter": RegFilter
};
