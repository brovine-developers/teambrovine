/**
 * breadcrumb.js
 *
 * Therin Irwin
 *
 * Sets up the Breadcrumb, which saves each piece of information about the
 * state of the page, such as the selected table rows and the regulation
 * filter data. This breadcrumb gets sent to the sever each time new
 * table data is requested.
 */
"use strict";

module.exports = (function () {
   var crumbData = {};
   var callbacks = {};

   var emit = function (table) {
      $.each(callbacks, function (query, funcs) {
         if (table.dt.is(query)) {
            $.each(funcs, function (i, item) {
               item(crumbData);
            });
         }
      });
   };

   var update = function (match, data, table) {
      var spec = {};
      spec[match] = data;
      $.extend(crumbData, spec);
      emit(table);
   };

   var register = function (match, func) {
      if (callbacks[match]) {
         callbacks[match].push(func);
      }
      else {
         callbacks[match] = [func];
      }
   };

   var registerDependencies = function (tableList, other) {
      if (tableList instanceof Array && tableList.length > 1) {
         if (tableList.length == 1) {
            return {"direct": tableList[0], "clear": []};
         }
         else {
            var curList = tableList[0];
            var deps = registerDependencies(tableList.splice(0, 1));

            for (var i = 0; i < curList.length; i++) {
               registerTableUpdate(curList, deps.clear, deps.direct[0], other);
            }

            return {"direct": curList, "clear": deps.direct.concat(deps.clear)};
         }
      }
   };

   var registerTableUpdate = function (from, clears, update, other) {
      register(from.id, function (crumbData) {
         update.dt.fnClearTable();

         $.each(clears, function (i, item) {
            item.dt.fnClearTable();
         });

         jQuery.get("ajax/" + update.schema.url,
            crumbData,
            function(data) {
               update.dt.fnClearTable();
               update.dt.fnAddData(data);
               if (other) other(data);
            },
            'json'
         );
      });
   };

   return {
      "update": update,
      "emit": emit,
      "register": register,
      "registerTableUpdate": registerTableUpdate
   };
}) ();
