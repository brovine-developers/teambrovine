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

var fixWidth = function (table) {
   table.css('width', '100%');

   $('.dataTables_filter input').each(function () {
      $(this).width($(this).parent().width() - 10);
   });
};

var fixAllWidths = function () {
   $('table.dataTable').each(function (i, item) {
      fixWidth($(item));
   });
};

var findColumn = function (mDataProp, schema) {
   var ret = -1;

   schema.aoColumns.forEach(function (val, idx) {
      if (val.mDataProp === mDataProp)
         ret = idx;
   });

   return ret;
};

var updateCrumb = function (table) {
   var specs = [];

   table.dt.$('tr.selected').each(function (i) {
      var row = table.dt.fnGetData(this);
      specs[i] = row[table.schema.crumb];
   });

   if (specs.length > 0) {
      breadcrumb.update(table.schema.crumb, specs, table);
      fixAllWidths();
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

   this.fixWidth = fixWidth.bind(this, this.dt);
   this.findColumn = function (mDataProp) { findColumn(mDataProp, that.dt); };
   this.updateCrumb = updateCrumb.bind(this, this);

   this.fixWidth();
   setupPlaceholder(this.dt);

   return this;
};

module.exports = {
   "Table": NewTable,
   "schema": tableSchema,
   "fixAllWidths": fixAllWidths
};
