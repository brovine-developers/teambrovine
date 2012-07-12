var curGeneid;
var experiment;
var tfReload;

// Update species list.
// 1st table on 1st row.
function updateSpeciesList() {
   updateSelectList(speciesList, "species", "getSpeciesList", {},
      function() {},
      function() {
         experimentList.fnClearTable();
         experiment = undefined;
         comparisonList.fnClearTable();
         tfSummary.fnClearTable();
         tfOccur.fnClearTable();
      },
      function(specs) {
         $('#sequenceInfo').empty();
         updateComparisonList(specs);
      });
}

// Updates the comparison table on TF Popularity
// 2nd table on 1st row.
function updateComparisonList(curSpecies) {
   updateSelectList(comparisonList, "comparisontypeid", "getComparisonList",
      { 'species': curSpecies },
      function() {},
      function() {
         experimentList.fnClearTable();
         experiment = undefined;
         tfSummary.fnClearTable();
         tfOccur.fnClearTable();
      },
      function(specs) {
         $('#sequenceInfo').empty();
         updateExperimentList(specs);
      });
}

// Updates the experiment list.
// 3rd table, first row.
function updateExperimentList(comparisontypeid) {
   updateSelectList(experimentList, "experimentid", "getExperimentList",
      { 'comparisontypeid': comparisontypeid },
      function() {},
      function() {
         tfSummary.fnClearTable();
         tfOccur.fnClearTable();
      },
      function(specs) {
         filters = updateTF();
         filters['experiment'] = specs;
         experiment = specs;
         updateTFSummary(filters);
      });
}

// Updates the transcription factor summary.
// 1st table, 2nd row.
function updateTFSummary(filters) {
   updateSelectList(tfSummary, "transfac", "getDistinctFactorList",
      filters,
      function() {},
      function() {
         tfOccur.fnClearTable();
      },
      function(specs) {
         updateTFOccurrences(filters.experiment, specs);
      });
}

function updateTFOccurrences(exp, tf) {
   jQuery.get("ajax/getTFOccur",
      {
         "expid": exp,
         "tf": tf
      },
      function(data) {
         tfOccur.fnClearTable();
         tfOccur.fnAddData(data);
         fixTableWidth(tfOccur);
      },
      'json'
   );
}

function setupExperimentHierarchy() {
   var firstRowHeight = "100px";
   var secondRowHeight = "150px";
   var thirdRowHeight = "150px";
   speciesList = $('#speciesList').dataTable({
      "sDom": "<'row'<'span2'f>r>t<'row'<'span2'i>>",
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
      "sDom": "<'row'<'span4'f>r>t<'row'<'span4'i>>",
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
   
   tfSummary = $('#tfList_summ').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Transcription Factors",
         "sInfo": "Showing _TOTAL_ transcription factors",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": secondRowHeight,
      "aoColumns": [
         {"sTitle": "Transfac", "mDataProp": "transfac"},
         {"sTitle": "Studies", "mDataProp": "numStudies"},
         {"sTitle": "Genes", "mDataProp": "numGenes"},
         {"sTitle": "Occurrences", "mDataProp": "numOccs"}
      ]

   });
   tfSummary.fnSort( [ [3, "desc"] ] );

   tfOccur = $('#tf_occur').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Transfac Occurrences",
         "sInfo": "Showing _TOTAL_ occurrences",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": secondRowHeight,
      "aoColumns": [
         {"sTitle": "Species", "mDataProp": "speciesPretty"},
         {"sTitle": "Comparison", "mDataProp": "celltype"},
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Gene Name", "mDataProp": "genename"},
         {"sTitle": "Gene Abbrev", "mDataProp": "geneabbrev"},
         {"sTitle": "Study", "mDataProp": "studyPretty"},        
         {"sTitle": "Beginning", "mDataProp": "beginning"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "Length", "mDataProp": "length"}
      ]
      
   });
   
   var tfReload;

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
         $("#sequenceFilterOptions input[type='text']").keyup(function () {
            clearTimeout(tfReload);

            tfReload = setTimeout(function () {
               filters = updateTF();
               filters['experiment'] = experiment;

               if (experiment !== undefined)
                  updateTFSummary(filters);
            }, 250);
         });
      },
      'json'
   );
   
   updateSpeciesList();
}

function updateTF() {
   var la = $("#minla").val();
   var la_s = $("#minlaslash").val();
   var lq = $("#minlq").val();
   var ld = $("#maxld").val();
         
   la = isNaN(parseInt(la)) ? 0 : la;
   la_s = isNaN(parseInt(la_s)) ? 0 : la_s;
   lq = isNaN(parseInt(lq)) ? 0 : lq;
   ld = isNaN(parseInt(ld)) ? 10000 : ld;

   return { 'minLa': la, 'minLaSlash': la_s, 'minLq': lq, 'maxLd': ld};
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(tfSummary);
   fixTableWidth(tfOccur);
}

// Just use window.load.
$(window).load(function() {
   setupExperimentHierarchy();
   setupPlaceholders();
   fixAllTableWidths();
});
