var curGeneid;
var curExps;
var regInput;
var geneModal;
var sequenceModal;
var matchModal;
var comparisonModal;
var experimentModal;
var showHidden;
var objShowHidden;
var Brovine = Brovine || {};

function getTimestamp() {
   // http://www.perturb.org/display/786_Javascript_Unixtime.html
   var foo = new Date; // Generic JS date object
   var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
   var unixtime = parseInt(unixtime_ms / 1000);
}

function getPrettyTime() {
   var now = new Date;
   var date = new Date();
   var yyyy = date.getFullYear();
   var mm = date.getMonth() + 1;
   var dd = date.getDate();
   var hh = date.getHours();
   var min = date.getMinutes();
   var ss = date.getSeconds();

   var mysqlDateTime = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min + ':' + ss;
   return mysqlDateTime;
}

/**
 * Given a dataTable object and an object with data to match, select the row matching
 * that data.
 */
function selectTableRow(table, matchData) {
   table.$('tr').each(function() {
      var rowData = table.fnGetData(this);
      var shouldSelect = true;
      for (name in matchData) {
         if (rowData[name] != matchData[name]) {
            shouldSelect = false;
            break;
         }
      }

      if (shouldSelect) {
         $(this).addClass('selected');
      }
   });
}

function getSelectedRow(table) {
   return table.$('tr.selected')[0];
}

function getSelectedRowData(table) {
   return table.fnGetData(getSelectedRow(table));
}

var getSelectedRowsData = function (table) {
   var data = []
   var rows = table.$('tr.selected');

   $.each(rows, function (i, item) {
      data.push(table.fnGetData(item));
   });

   return data;
}

function getPromoter(geneid) {
   jQuery.get("ajax/getPromoter", {
      geneid: geneid
   },
   function(data) {
      $('#genepromoterInput').removeClass('disabled').html(data.promoter);
   }, 'json');

}

function updateSpeciesListData(data) {
   speciesList.fnClearTable();
   speciesList.fnAddData(data);
   
   speciesList.$('tr').click(function(e) {
      speciesList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = speciesList.fnGetData(this);

      var curSpecies = rowData.species;
      experimentList.fnClearTable();
      geneList.fnClearTable();
      factorList.fnClearTable();
      sequenceList.fnClearTable();
      $('#sequenceInfo').addClass('hidden');
      updateComparisonList(curSpecies);
      fixAllTableWidths();
   });
}

function updateSpeciesList() {
   updateMultiselectList(speciesList, "species", "getSpeciesList",
      { showHidden : showHidden },
      function () {
         experimentList.fnClearTable();
         geneList.fnClearTable();
         factorList.fnClearTable();
         sequenceList.fnClearTable();
      },
      function (specs) {
         updateComparisonList(specs);
         fixAllTableWidths();
      }
   );
}

function updateSequenceInfoData(data) {
   $("#sequenceStart").html(data.sequenceInfo.beginning);
   $("#sequenceLength").html(data.sequenceInfo.length);
   $("#sequenceSense").html(data.sequenceInfo.sense);
   $("#sequenceSequence").html(data.sequenceInfo.sequence);
   $("#sequenceGene").html(data.sequenceInfo.genename + " (" + 
    data.sequenceInfo.geneabbrev + ")");
   $("#sequenceSpecies").html(data.sequenceInfo.species);
   $("#sequenceComparison").html(data.sequenceInfo.celltype);
   $("#sequenceExperiment").html(data.sequenceInfo.label);

   similarList.fnClearTable();
   similarList.fnAddData(data.sequenceInfo.similar);

   matchList.fnClearTable();
   matchList.fnAddData(data.factorMatchInfo);

   matchList.$('tr').click(function(e) {
      matchList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = matchList.fnGetData(this);

      var matchid = rowData.matchid;

      fixAllTableWidths();
   });


   $("#sequenceInfo").removeClass("hidden");
   fixTableWidth(similarList);
   fixTableWidth(matchList);
}

function updateSequenceInfo(seqid) {
   jQuery.get("ajax/getSequenceInfo",
   {
      'seqid': seqid,
      'showHidden': showHidden
   },
   function(data) {
      updateSequenceInfoData(data);
   }, 'json');
}

function updateSequenceListData(data) {
   sequenceList.fnClearTable();
   sequenceList.fnAddData(data);
   fixTableWidth(sequenceList);

   sequenceList.$('tr').click(function() {
      sequenceList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = sequenceList.fnGetData(this);

      updateSequenceInfo(rowData.seqid);
   });
}

function updateSequenceList(geneid, transfac) {
   jQuery.get("ajax/getSequenceList",
   {
      'geneid': geneid,
      'transfac': transfac,
      'showHidden': showHidden
   },
   function(data) {
      $('#sequenceInfo').addClass('hidden');
      updateSequenceListData(data);
   },
   'json'
   );
}

function updateFactorListData(data) {
   factorList.fnClearTable();
   factorList.fnAddData(data);
   fixTableWidth(factorList);
   factorList.$('tr').click(function(e) {
      factorList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = factorList.fnGetData(this);
      var transfac = rowData.transfac;
      var study = rowData.study;
      updateSequenceList(curGeneid, transfac, study);
   });
}

function updateFactorList(geneids) {
   updateMultiselectList(factorList, "transfac", "getFactorList",
      {
         'geneid': geneids,
         'expid': curExps,
         'showHidden': showHidden
      },
      function () {
         sequenceList.fnClearTable();
         $('#sequenceInfo').addClass('hidden');
      },
      function (specs) {
         updateSequenceList(curGeneid, specs);
      });
}

function updateGeneListData(data) {
   geneList.fnClearTable();
   geneList.fnAddData(data);
   fixTableWidth(geneList);
   geneList.$('tr').click(function(e) {
      geneList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = geneList.fnGetData(this);
      curGeneid = rowData.geneid;
      var expid = getSelectedRowData(experimentList).experimentid;
      updateFactorList(expid);
   });
}

function updateGeneList(experimentid) {
   updateMultiselectList(geneList, "geneid", "getGeneList",
      {
         'experimentid': experimentid,
         'showHidden': showHidden
      },
      function () {
         factorList.fnClearTable();
         sequenceList.fnClearTable();
      },
      function (specs) {
         curGeneid = specs;

         updateFactorList(specs);
         fixAllTableWidths();
      });
}

function updateExperimentListData(data) {
   experimentList.fnClearTable();
   experimentList.fnAddData(data);
   fixTableWidth(experimentList);
   experimentList.$('tr').click(function(e) {
      experimentList.$('tr').removeClass('selected');
      $(this).addClass('selected');

      var rowData = experimentList.fnGetData(this);
      var experimentid = rowData.experimentid;
      updateGeneList(experimentid);
   });
}


function updateExperimentList(comparisontypeid) {
   updateMultiselectList(experimentList, "experimentid", "getExperimentList",
      {
         "comparisontypeid": comparisontypeid,
         showHidden : showHidden
      },
      function () {
         geneList.fnClearTable();
         factorList.fnClearTable();
         sequenceList.fnClearTable();
      },
      function (specs) {
         curExps = specs;

         updateGeneList(specs);
         fixAllTableWidths();
      }
   );
}

function updateComparisonList(curSpecies) {
   updateMultiselectList(comparisonList, "comparisontypeid", "getComparisonList",
      { 
         species: curSpecies,
         showHidden : showHidden
      },
      function () {
         experimentList.fnClearTable();
         geneList.fnClearTable();
         factorList.fnClearTable();
         sequenceList.fnClearTable();
      },
      function (specs) {
         updateExperimentList(specs);
         fixAllTableWidths();
      }
   );
}

function setupExperimentHierarchy() {
   var firstRowHeight = "100px";
   var secondRowHeight = "150px";
   var thirdRowHeight = "150px";
   speciesList = $('#speciesList').dataTable({
      "sDom": "<<f>r>t<i>>",
      "sPaginationType": "bootstrap",
      "bPaginate": false,
      "bInfo": false,
      "sScrollY": firstRowHeight,
      "oLanguage": {
         "sSearch": "Search Species"
      },
      "aoColumns": [
         {"sTitle": "Species", "mDataProp": "speciesPretty"},
         {"sTitle": "SpeciesLower", "mDataProp": "species", "bVisible": false}
      ]
   });

   comparisonList = $('#comparisonList').dataTable({
      "sDom": "<<f>r>t<i>>",
      "sPaginationType": "bootstrap",
      "bPaginate": false,
      "bInfo": false,
      "sScrollY": firstRowHeight,
      "oLanguage": {
         "sSearch": "Search Comparisons"
      },
      "aoColumns": [
         {"sTitle": "Comparison", "mDataProp": "comparison"},
         {"sTitle": "ComparisonTypeId", "mDataProp": "comparisontypeid", "bVisible": false}
      ]
   });

   experimentList = $('#experimentList').dataTable( {
      "sDom": "<'row'<'span6'f>r>t<'row'<'span6'i>>",
      "sPaginationType": "bootstrap",
      "bPaginate": false,
      "bInfo": false,
      "sScrollY": firstRowHeight,
      "oLanguage": {
         "sSearch": "Search Experiments"
      },
      "aoColumns": [
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Gene Count", "mDataProp": "genecount_all"},
         {"sTitle": "Experimentid", "mDataProp": "experimentid", "bVisible": false}
      ]
   });

   geneCols = [
      {"sTitle": "Gene", "mDataProp": "geneabbrev"},
      {"sTitle": "Species", "mDataProp": "species"},
      {"sTitle": "Comparison", "mDataProp": "celltype"},
      {"sTitle": "Experiment", "mDataProp": "label"},
      {"sTitle": "Reg", "mDataProp": "regulation"},
      {"sTitle": "Geneid", "mDataProp": "geneid", "bVisible": false},
      {"sTitle": "GeneName", "mDataProp": "genename", "bVisible": false}
   ];

   geneList = $('#geneList').dataTable({
      "sDom": "<'row'<'span8'f>r>t<'row'<'span3'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Genes",
         "sInfo": "Showing _TOTAL_ genes",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": secondRowHeight,
      "aoColumns": geneCols
   });

   factorCols = [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "(#) Genes", "mDataProp": "numGenes"},
         {"sTitle": "(#) Occurs", "mDataProp": "numTimes"},
         {"sTitle": "AllRow", "mDataProp": "allRow", "bVisible": false}
   ];
   
   factorList = $('#factorList').dataTable({
      "sDom": "<'row'<'span4'f>r>t<'row'<'span4'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "oLanguage": {
         "sSearch": "Search Transcription Factors"
      },
      "aoColumns": factorCols,
      "aaSortingFixed": [[2,'desc']]
   });
   
   sequenceList = $('#sequenceList').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "sScrollY": thirdRowHeight,
      "oLanguage": {
         "sSearch": "Search Regulatory Sequences"
      },
      "aoColumns": [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "Gene", "mDataProp": "geneabbrev"},
         {"sTitle": "Species", "mDataProp": "species"},
         {"sTitle": "Begin", "mDataProp": "beginning"},
         {"sTitle": "Length", "mDataProp": "length"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "La", "mDataProp": "la", "sType": "numeric"},
         {"sTitle": "La/", "mDataProp": "la_slash", "sType": "numeric"},
         {"sTitle": "Lq", "mDataProp": "lq", "sType": "numeric"},
         {"sTitle": "Ld", "mDataProp": "ld", "sType": "numeric"},
         {"sTitle": "Lpv", "mDataProp": "lpv", "sType": "numeric"},
         {"sTitle": "Sequence", "mDataProp": "sequence"},
         {"sTitle": "Sequenceid", "mDataProp": "seqid", "bVisible": false},
         {"sTitle": "GeneName", "mDataProp": "genename", "bVisible": false}
      ]
   
   });
   
   similarList = $('#similarList').dataTable({
      "sDom": "<'row'<'span6'f>r>t<'row'<'span6'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Similar Sequences"
      },
      "sScrollY": "200px",
      "aoColumns": [
         {"sTitle": "Begin", "mDataProp": "beginning"},
         {"sTitle": "Length", "mDataProp": "length"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "Sequence", "mDataProp": "sequence"}
      ]
   });
   
   matchList = $('#matchList').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "sScrollY": thirdRowHeight,
      "oLanguage": {
         "sSearch": "Search Matching Factors"
      },
      "aoColumns": [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "Study", "mDataProp": "studyPretty"},
         {"sTitle": "La", "mDataProp": "la", "sType": "numeric"},
         {"sTitle": "La/", "mDataProp": "la_slash", "sType": "numeric"},
         {"sTitle": "Lq", "mDataProp": "lq", "sType": "numeric"},
         {"sTitle": "Ld", "mDataProp": "ld", "sType": "numeric"},
         {"sTitle": "Lpv", "mDataProp": "lpv", "sType": "numeric"},
         {"sTitle": "Sc", "mDataProp": "sc", "sType": "numeric"},
         {"sTitle": "Sm", "mDataProp": "sm", "sType": "numeric"},
         {"sTitle": "Spv", "mDataProp": "spv", "sType": "numeric"},
         {"sTitle": "Ppv", "mDataProp": "ppv", "sType": "numeric"}
      ]
   
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

   $('#geneList_wrapper tr th:contains(Chr)').tooltip({
      title: "Chromosome"
   });
   
   $('#geneList_wrapper tr th:contains(Reg)').tooltip({
      title: "Regulation"
   });
   
   $('#geneList_wrapper tr th:contains(Gene)').tooltip({
      title: "Hover for full name"
   });
   
   $('#geneList_wrapper tr th:contains(Factors)').tooltip({
      title: "Unique factor / study pairs"
   });
   
   $('#experimentList_wrapper tr th:contains(Up)').tooltip({
      title: "Genes regulated up"
   });
   
   $('#experimentList_wrapper tr th:contains(Down)').tooltip({
      title: "Genes regulated down"
   });
   
   $('#experimentList_wrapper tr th:contains(Genes)').tooltip({
      title: "Total number of genes"
   });
   
   $("#factorList_wrapper tr th:contains('#')").tooltip({
      title: "Number of matching regulatory elements"
   });

   // Setup Regulatory Sequence filter
   var minLaVal;
   var minLaSlashVal;
   var minLqVal;
   var maxLdVal;
   var minBegVal;
   var maxBegVal;
   var senseFilterVal;

   var updateSequenceFilter = function() {
      minLaVal = $('#minla').val();
      minLaSlashVal = $('#minlaslash').val();
      minLqVal = $('#minlq').val();
      maxLdVal = $('#maxld').val();
      minBegVal = $('#minbeg').val();
      maxBegVal = $('#maxbeg').val();

      if (minLaVal == '') {
         minLaVal = -99999;
      } else {
         minLaVal = minLaVal * 1.0;
      }

      if (minLaSlashVal == '') {
         minLaSlashVal = -99999;
      } else {
         minLaSlashVal = minLaSlashVal * 1.0;
      }

      if (minLqVal == '') {
         minLqVal = -99999;
      } else {
         minLqVal = minLqVal * 1.0;
      }
      
      if (maxLdVal == '') {
         maxLdVal = 99999;
      } else {
         maxLdVal = maxLdVal * 1.0;
      }
      
      if (minBegVal == '') {
         minBegVal = -99999;
      } else {
         minBegVal = minBegVal * 1.0;
      }
      
      if (maxBegVal == '') {
         maxBegVal = 99999;
      } else {
         maxBegVal = maxBegVal * 1.0;
      }

      senseFilterVal = $("#senseFilters input[type='radio']:checked").val();
   };

   updateSequenceFilter();
   
   var filterSequenceList = function(oSettings, aData, iDataIndex) {
      if (oSettings.sTableId != "sequenceList") {
         return true;
      }

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

   $.fn.dataTableExt.afnFiltering.push(filterSequenceList);

   var triggerSequenceListRedraw = function() {
      updateSequenceFilter();
      sequenceList.fnDraw();

   };

   // Add radio button listener to redraw table.
   $('#senseFilters input').change(triggerSequenceListRedraw);
   
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
         
         $("#sequenceFilterOptions input[type='text']").val("");
         
         var inc = 0;
         
         jQuery.each(points, function (i, val) {
            $("#sequenceFilterOptions input[type='text']:eq(" + inc + ")").attr("placeholder", val.min + " - " + val.max);
            inc++;
         });
         
         $("#sequenceFilterOptions input[type='text']").removeAttr("disabled");
         $("#sequenceFilterOptions input[type='text']").keyup(triggerSequenceListRedraw);
      },
      'json'
   );

   // Get the list of species from the server.
   updateSpeciesList();
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(geneList);
   fixTableWidth(factorList);
   fixTableWidth(sequenceList);
   fixTableWidth(similarList);
   fixTableWidth(matchList);
}  

$(document).ready(function() {
   setupExperimentHierarchy();
   setupPlaceholders();
   fixAllTableWidths();
});

var getVisibleColumns = function (cols) {
   var vis = [];

   $.each(cols, function (i, item) {
      if (item.bVisible === undefined || item.bVisible === true)
         vis.push(item);
   });

   return vis;
};

var strFlatten = function (delim, arr, elem) {
   var str = "";

   $.each(arr, function (i, item) {
      if (i !== 0)
         str += delim;

      str += item[elem].toString();
   });

   return str;
};

// Work around for chrome. Sometimes, it doesn't properly fix tables.
$(window).load(function() {
   var dataToCsv = function (data, objs) {
      var ans = "";
      var start = true;

      for (var i = 0; i < data.length; i++) {
         start = true;

         for (key in objs) {
            if (!start)
               ans += ", ";

            ans += data[i][objs[key].mDataProp];
            start = false;
         }

         ans += "\n";
      }

      return ans;
   };

   var headerCreate = function (objs) {
      var ans = "";

      for (key in objs)
         ans += objs[key].mDataProp + ", ";

      return ans + "\n";
   };

   var visGeneCols = getVisibleColumns(geneCols);
   var visFactorCols = getVisibleColumns(factorCols);

   $("#geneExport").localDownload({
      "func": function () {
         var headers = headerCreate(visGeneCols);
         var data = dataToCsv(geneList.fnGetData(), visGeneCols);
         return data && data.length != 0 ? headers + data : false;
      },
      "filename": function () {
         return "gene-data-" + strFlatten("-",
            getSelectedRowsData(experimentList), "label") + ".csv";
      }
   });

   $("#factorExport").localDownload({
      "func": function () {
         var headers = headerCreate(visFactorCols);
         var data = dataToCsv(factorList.fnGetData(), visFactorCols);
         return data && data.length != 0 ? headers + data : false;
      },
      "filename": function () {
         return "factor-data-" + strFlatten("-", getSelectedRowsData(geneList),
            "geneabbrev") + ".csv";
      }
   });

   $("#selectAllGenes").click(function (e) {
      var specs = [];
      var items = $("#geneList").find("tr");

      items.removeClass("selected");
      items.addClass("selected");

      geneList.$('tr.selected').each(function(i) {
         var rowData = geneList.fnGetData(this);
         specs[i] = rowData["geneid"];
      });

      curGeneid = specs;

      updateFactorList(specs);
      fixAllTableWidths();
   });

   fixAllTableWidths();

   var regInput = Brovine.newRegInput("#regFilter", function() {
      geneList.fnDraw();
   });

   $.fn.dataTableExt.afnFiltering.push(regInput.filter("geneList", 4));
});
