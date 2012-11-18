var breadcrumb = require('./breadcrumb');

var tables = [];

var filterSequenceList = function (oSettings, aData, iDataIndex, table) {
   return (
      // Check Begin
      aData[3] >= minBegVal && 
      aData[3] <= maxBegVal && 
      // Check Sense
      (aData[5] == senseFilterVal || senseFilterVal == 'all') &&
      // Check L-Values
      aData[6] >= minLaVal &&
      aData[7] >= minLaSlashVal &&
      aData[8] >= minLqVal &&
      aData[9] <= maxLdVal
   );
};

var init = function (id) {
   // Register the update callback
   breadcrumb.register(id, function (data) {
      $.each(tables, function (i, table) {
         table.fnDraw();
      });
   });

   // Initialize the change handlers for inputs
   $(id).on('change', 'input', function (event) {
      var target = $(event.currentTarget);

      if (target.is("[type=radio]")) {
         breadcrumb.update(target.closest("[id]").attr('id'), target.val(),
          { "dt": $(id) });
      }
      else {
         breadcrumb.update(target.attr('id'), target.val(), { "dt": $(id) });
      }
   });

   jQuery.get("ajax/getMetricExtremes",
      function(data) {
         jQuery.each(data, function (i, val) {
            data[i] = parseFloat(val);
         });
         
         points = {
            'la': { min: data.la_min, max: data.la_max},
            'la_slash': { min: data.las_min, max: data.las_max},
            'lq': { min: data.lq_min, max: data.lq_max},
            'ld': { min: data.ld_min, max: data.ld_max}
         };
         
         $(id + " input[type='text']").val("");
         
         var idx = 0;

         jQuery.each(points, function (i, val) {
            $(id + " input[type='text']:eq(" + idx + ")")
             .attr("placeholder", val.min + " - " + val.max);
            idx++;
         });
         
         $(id + " input[type='text']").removeAttr("disabled");
      },
      'json'
   );
};

var addTable = function (table) {
   tables.push(table.dt);
   
   $.fn.dataTableExt.afnFiltering.push(function (oSettings, aData, iDataIndex) {
      if (oSettings.sTableId == table.id) {
         filterSequenceList(oSettings, aData, iDataIndex, table);
      }
   });
};


module.exports = {
   "add": addTable,
   "init": init
};
