(function(){var global = this;function debug(){return debug};function require(p, parent){ var path = require.resolve(p) , mod = require.modules[path]; if (!mod) throw new Error('failed to require "' + p + '" from ' + parent); if (!mod.exports) { mod.exports = {}; mod.call(mod.exports, mod, mod.exports, require.relative(path), global); } return mod.exports;}require.modules = {};require.resolve = function(path){ var orig = path , reg = path + '.js' , index = path + '/index.js'; return require.modules[reg] && reg || require.modules[index] && index || orig;};require.register = function(path, fn){ require.modules[path] = fn;};require.relative = function(parent) { return function(p){ if ('debug' == p) return debug; if ('.' != p.charAt(0)) return require(p); var path = parent.split('/') , segs = p.split('/'); path.pop(); for (var i = 0; i < segs.length; i++) { var seg = segs[i]; if ('..' == seg) path.pop(); else if ('.' != seg) path.push(seg); } return require(path.join('/'), parent); };};require.register("lib/Brovine.js", function(module, exports, require, global){
require('./helpers/local-download');

module.exports = {
   "breadcrumb": require('./breadcrumb'),
   "table": require('./table'),
   "lvalueFilter": require('./lvalue-filter'),
   "tooltip": require('./helpers/tooltips'),
   "regInput": require('./helpers/reg-input'),
   "selectAll": require('./helpers/select-all')
};

});require.register("lib/breadcrumb.js", function(module, exports, require, global){
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

});require.register("lib/helpers/local-download.js", function(module, exports, require, global){
(function ($) {
    
    /**
     * Create a link with embedded data which the user can download.
     * Useful when there's already local data that the user may want
     * to save, such as a table.
     * 
     * Options:
     *  - opts.filename: name that should be used when saving the file
     *  - opts.data: data that will be saved to the user's computer if the link
     *     is clicked. If this is null or empty (""), this function does
     *     nothing. Should be a string. Data will be URI encoded before being
     *     embedded in the link.
     *  - opts.func: function to be called on click to automatically update the
     *     data embedded in the link. If this is undefined or null, no click
     *     handler will be set. Must be a function. If the return value from
     *     this function is blank, false, etc., 
     */
    $.fn.localDownload = function (opts) {
        // Set up default settings for filename
        var settings = $.extend({
            "filename": "temp_file",
            "local": true, // assume the browser does support local download.
            "serverType": "form", // used when local=false. one of "GET" or "form".
            "bounceUrl": "/ajax/saveFile"
        }, opts);

        var setHref = function (settings, data, scope) {
            var header = (settings.local) ? "data:octet-stream," : settings.bounceUrl;

            if (!data) {
                $(scope).attr("href", "#");
            }
            else {
                var requestLink;

                if (settings.local)
                    requestLink = header + encodeURI(data);
                /*else if (settings.serverType === "POST")
                    requestLink = header;
                */
                else if (settings.serverType === "form")
                    requestLink = "#";

                $(scope).attr("href", requestLink);
            }
        };

        var createForm = function (data, url, filename) {
            return $('<form></form>').hide()
              .attr({ target: '_blank', method: 'post', action: url })
              .append($('<input />')
                .attr({ type: 'hidden', name: 'data', value: data })
              )
              .append($('<input />')
                .attr({ type: 'hidden', name: 'filename', value: filename })
              );
        };
        
        // If there's data, turn every element into a download link
        return this.each(function () {
            setHref(settings, settings.data, this);
            
            if (!(settings.filename instanceof Function))
                $(this).attr("download", settings.filename);
            
            if (settings.func) {
                $(this).click(function (event) {
                    var data = settings.func(); 

                    if (data) {
                        if (settings.filename instanceof Function)
                            $(this).attr("download", settings.filename());

                        setHref(settings, data, this);

                        if (!settings.local) {
                            /*if (settings.serverType === "POST") {
                                $.ajax({
                                    url: settings.bounceUrl,
                                    type: "POST",
                                    data: data,
                                    success: function (xhr, status, err) {

                                    },
                                    error: function (data, status, xhr) {

                                    }
                                });

                                return false;
                            }
                            else*/ if (settings.serverType === "form") {
                                createForm(data, settings.bounceUrl
                                  , settings.filename).submit();
                                return false;
                            }
                        }
                    }
                    else {
                        alert("No data to download");
                        return false;
                    }
                });
            }
        }); // but do nothing if there's no data
    };
    
} (jQuery));

});require.register("lib/helpers/reg-input.js", function(module, exports, require, global){
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

});require.register("lib/helpers/select-all.js", function(module, exports, require, global){
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

});require.register("lib/helpers/tooltips.js", function(module, exports, require, global){
// Tooltips
module.exports = (function () {
   var tooltips = {
      'th': {
         'Chr': 'Chromosome',
         'Reg': 'Regulation',
         'Gene': 'Hover for full name',
         'Factors': 'Unique factor / study pairs',
         'Genes': 'Total number of genes',
         '#': 'Number of matching regulatory elements'
      }
   };

   var init = function () {
      // th (table header) based tooltips
      $.each(tooltips.th, function (key, value) {
         $('th:contains(' + key + ')').tooltip({
            'title': value
         });
      });

      // Setup tooltips on gene rows.
      $('#geneList').tooltip({
         selector: 'td:first-child',
         title: function() {
            if ($(this).hasClass('dataTables_empty')) {
               return "Select an experiment first.";
            }
            var row = ($(this).parent()[0]);
            var rowData = geneList.fnGetData(row);
            return rowData.genename;
         }
      });

      // Setup tooltips on gene rows in the sequence table.
      $('#sequenceList').tooltip({
         selector: 'td:nth-child(2)',
         title: function() {
            if ($(this).hasClass('dataTables_empty')) {
               return "Select a factor first.";
            }
            var row = ($(this).parent()[0]);
            var rowData = sequenceList.fnGetData(row);
            return rowData.genename;
         }
      });
   };

   return {
      "init": init
   };
}) ();

});require.register("lib/lvalue-filter.js", function(module, exports, require, global){
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

});require.register("lib/table.js", function(module, exports, require, global){
/**
 * table.js
 *
 * Therin Irwin
 *
 * Creates new dataTables and sets up their click handlers.
 */
"use strict";

var breadcrumb = require("./breadcrumb");

var tableSchema = {
   "species": {
      "crumb": "species",
      "url": "getSpeciesList",
      "sSearch": "Search Species",
      "aoColumns": [
         {"sTitle": "Species", "mDataProp": "speciesPretty"},
         {"sTitle": "SpeciesLower", "mDataProp": "species", "bVisible": false}
      ]
   },

   "comparison": {
      "crumb": "comparisontypeid",
      "url": "getComparisonList",
      "sSearch": "Search Comparisons",
      "aoColumns": [
         {"sTitle": "Comparison", "mDataProp": "comparison"},
         {"sTitle": "ComparisonTypeId", "mDataProp": "comparisontypeid", "bVisible": false}
      ]
   },

   "experiment": {
      "crumb": "experimentid",
      "url": "getExperimentList",
      "sSearch": "Search Experiments",
      "aoColumns": [
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Gene Count", "mDataProp": "genecount_all"},
         {"sTitle": "Experimentid", "mDataProp": "experimentid", "bVisible": false}
      ]
   },

   "gene": {
      "crumb": "geneid",
      "url": "getGeneList",
      "sSearch": "Search Genes",
      "aoColumns": [
         {"sTitle": "Gene", "mDataProp": "geneabbrev"},
         {"sTitle": "Species", "mDataProp": "species"},
         {"sTitle": "Comparison", "mDataProp": "celltype"},
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Reg", "mDataProp": "regulation"},
         {"sTitle": "Geneid", "mDataProp": "geneid", "bVisible": false},
         {"sTitle": "GeneName", "mDataProp": "genename", "bVisible": false}
      ]
   },

   "factor": {
      "crumb": "transfac",
      "url": "getFactorList",
      "sSearch": "Search Transfacs",
      "aoColumns": [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "(#) Genes", "mDataProp": "numGenes"},
         {"sTitle": "(#) Occurs", "mDataProp": "numTimes"},
         {"sTitle": "AllRow", "mDataProp": "allRow", "bVisible": false}
      ]
   }
};

var NewTable = function (id, schema, height) {
   var that = this;

   if (!(this instanceof NewTable)) {
      return new NewTable(id, schema, height);
   }

   this.dt = $(id).dataTable({
      "sDom": "<<f>r>t<i>",
      "sPaginationType": "bootstrap",
      "bFilter": true,
      "bPaginate": false,
      "bInfo": false,
      "sScrollY": height,
      "oLanguage": { "sSearch": schema.sSearch },
      "aoColumns": schema.aoColumns
   });

   this.dt.on('click', 'tr', function (e) {
      if (!e.metaKey && !e.ctrlKey) {
         that.dt.$('tr').removeClass('selected');
      }

      $(this).toggleClass('selected');
      that.updateCrumb();
   });

   this.schema = schema;
   this.id = id;

   this.fixWidth();
   setupPlaceholder(this.dt);

   return this;
};

// Private
var setupPlaceholder = function (table) {
   var label = table.parents(".dataTables_wrapper").find(".dataTables_filter label");
   var text = label.text();
   var input = label.children('input');

   for (var i = 0; i < label[0].childNodes.length; i++) {
      var child = label[0].childNodes[i];
      if (child.nodeType == 3) {
         label[0].removeChild(child);
      }
   } 

   input.attr('placeholder', text);
   input.width($(this).parent().width() - 10);
};

NewTable.prototype.fixWidth = function () {
   this.dt.css('width', '100%');

   $('.dataTables_filter input').each(function () {
      $(this).width($(this).parent().width() - 10);
   });
};

var fixAllWidths = function () {
   $('table.dataTable').each(function (i, item) {
      //fixWidth($(item));
   });
};

NewTable.prototype.findColumn = function (mDataProp) {
   var ret = -1;

   this.schema.aoColumns.forEach(function (val, idx) {
      if (val.mDataProp === mDataProp)
         ret = idx;
   });

   return ret;
};

NewTable.prototype.getSelectedData = function (col) {
   var specs = [],
       that = this;

   this.dt.$('tr.selected').each(function (i) {
      var row = that.dt.fnGetData(this);
      specs[i] = row[col];
   });

   return specs;
};

NewTable.prototype.updateCrumb = function () {
   var specs = this.getSelectedData(this.schema.crumb);

   if (specs.length > 0) {
      breadcrumb.update(this.schema.crumb, specs, this);
      fixAllWidths();
   }

   return specs;
};

// Gets the visible columns of the table.
NewTable.prototype.getVisibleColumns = function () {
   var vis = [];

   this.schema.aoColumns.forEach(function (col) {
      if (col.bVisible === undefined || col.bVisible)
         vis.push(col);
   });

   return vis;
};

// Returns a list of the header names for the table. Ignores columns that
// aren't visible.
NewTable.prototype.getVisibleColumnNames = function () {
   var headers = [],
       columns = this.getVisibleColumns();

   columns.forEach(function (col) {
      headers.push(col.mDataProp);
   });

   return headers;
};

// Transforms table data into CSV format. Ignores columns that aren't visible.
NewTable.prototype.getCSVData = function (includeHeaders) {
   var data = this.dt.fnGetData(),
      visCols = this.getVisibleColumnNames(),
      csvData = "",
      start = true,
      inclHdrs = includeHeaders || true,
      hdrData;

   hdrData = visCols.reduce(function (prev, cur) {
      return "" + prev + ", " + cur;
   }) + "\n";

   for (var i = 0; i < data.length; i++) {
      start = true;

      visCols.forEach(function (val) {
         if (!start)
            csvData += ", ";

         csvData += data[i][val];
         start = false;
      });

      csvData += "\n";
   }

   return inclHdrs ? (hdrData || "") + (csvData || "") : (csvData || "");
};

// Constructs a unique name for the data in this table based on the rows of the
// table that are selected.
NewTable.prototype.getUniqueName = function (col, prefix) {
   var prefix = prefix || "brovine-data-",
       selected = this.getSelectedData(col);

   return prefix + selected.reduce(function (prev, cur) {
      return "" + prev + "-" + cur;
   });
};   

module.exports = {
   "Table": NewTable,
   "schema": tableSchema,
   "fixAllWidths": fixAllWidths
};

});var exp = require('lib/Brovine');if ("undefined" != typeof module) module.exports = exp;else Brovine = exp;
})();
$(window).load(function () {
   var Table = Brovine.table.Table;
   var schemas = Brovine.table.schema;
   var bread = Brovine.breadcrumb;
   var SelectAll = Brovine.selectAll;
   var RegFilter = Brovine.regInput.RegFilter;
   var LvalueFilter = Brovine.lvalueFilter;

   // Create all of the table instances
   var speciesList = new Table("#speciesList", schemas.species, "100px"),
       comparisonList = new Table("#comparisonList", schemas.comparison, "100px"),
       experimentList = new Table("#experimentList", schemas.experiment, "100px"),
       geneList1 = new Table("#geneList1", schemas.gene, "150px"),
       geneList2 = new Table("#geneList2", schemas.gene, "150px"),
       factorList1 = new Table("#factorList1", schemas.factor, "150px"),
       factorList2 = new Table("#factorList2", schemas.factor, "150px"),
       subtractList = new Table("#subtractList", schemas.factor, "150px");

   // Set up regulation filters
   var regFilter1 = new RegFilter("#regFilter1", geneList1.dt, 4),
       regFilter2 = new RegFilter("#regFilter2", geneList2.dt, 4);

   // Initialize select all buttons
   SelectAll("#selectAllGenes1", geneList1);
   SelectAll("#selectAllGenes2", geneList2);

   // Register the update handlers for each table 
   bread.registerTableUpdate(speciesList,
     [experimentList, geneList1, geneList2, factorList1, factorList2], comparisonList);
   bread.registerTableUpdate(comparisonList,
     [geneList1, geneList2, factorList1, factorList2], experimentList);
   bread.registerTableUpdate(experimentList, [factorList1, factorList2], geneList1);
   bread.registerTableUpdate(experimentList, [factorList1, factorList2], geneList2);

   // Register the reg. sequence filter options
   LvalueFilter.init('#sequenceFilterOptions');

   // Register the subtraction list updater
   var updateSubtract = function () {
      var data = [];
      var data1 = factorList1.dt._('tr', {"filter":"applied"});
      var data2 = factorList2.dt._('tr', {"filter":"applied"});

      if (data1 && data2 && data1.length !== 0 && data2.length !== 0) {
         for (var i = 0; i < data1.length; i++) {
            var include = true;

            for (var j = 0; j < data2.length; j++) {
               if (data1[i].transfac === data2[j].transfac) {
                  include = false;
                  break;
               }
            }

            if (include) {
               data.push(data1[i]);
            }
         }

         subtractList.dt.fnClearTable();
         subtractList.dt.fnAddData(data);
      }
   };

   // Register some more update handlers, and a special handler to update the
   // gene lists when the l-value filter is changed.
   bread.registerTableUpdate(geneList1, [], factorList1, updateSubtract);
   bread.registerTableUpdate(geneList2, [], factorList2, updateSubtract);
   bread.register('#sequenceFilterOptions', function () {
      geneList1.updateCrumb();
      bread.emit(geneList1);
      geneList2.updateCrumb();
      bread.emit(geneList2);
   });

   // Future thoughts: make dependency registration as easy as a nested array.
   var depList = [
      [speciesList],
      [comparisonList],
      [experimentList],
      [geneList1, geneList2],
      [factorList1, factorList2]
   ];

   // Set up local download buttons
   $("#geneExport1").localDownload({
      "func": geneList1.getCSVData.bind(geneList1),
      "filename": experimentList.getUniqueName.bind(experimentList, "label", "gene-data-")
   });

   $("#geneExport2").localDownload({
      "func": geneList2.getCSVData.bind(geneList2),
      "filename": experimentList.getUniqueName.bind(experimentList, "label", "gene-data-")
   });

   $("#subtractExport").localDownload({
      "func": subtractList.getCSVData.bind(subtractList),
      "filename": geneList1.getUniqueName.bind(geneList1, "geneabbrev", "factor-data-")
   });

   // Get the initial data for the species list
   jQuery.get("ajax/getSpeciesList",
      {},
      function (data) {
         speciesList.dt.fnClearTable();
         speciesList.dt.fnAddData(data);
      },
      'json'
   );
});
