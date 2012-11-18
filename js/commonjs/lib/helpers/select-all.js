/**
 * helpers/select-all.js
 *
 * Therin Irwin
 *
 * When 'button' is clicked, all of the rows in 'table' will be selected.
 */
"use strict";

var breadcrumb = require('../breadcrumb');

module.exports = function (button, table) {
   $(button).on('click', function (e) {
      var specs = [];
      var items = table.dt.find('tr');

      items.removeClass('selected');
      items.addClass('selected');

      table.dt.$('tr.selected').each(function (i) {
         var row = table.dt.fnGetData(this);
         specs[i] = row[table.schema.crumb];
      });

      if (specs.length > 0) {
         breadcrumb.update(table.schema.crumb, specs, table);
      }
   });
};
