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

   "geneInclude": {
      "crumb": "include",
      "dataProp": "geneid",
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

   "geneExclude": {
      "crumb": "exclude",
      "dataProp": "geneid",
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
      "url": "getFactorSubtract",
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
   var schm = this.schema;
   // If dataProp is set, use that as data column; otherwise use the crumb
   var propName = (schm.dataProp)? schm.dataProp : schm.crumb;
   var specs = this.getSelectedData(propName);

   if (specs.length > 0) {
      breadcrumb.update(schm.crumb, specs, this);
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
