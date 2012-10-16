var Brovine = Brovine || {};

var transFacs = [];
var studies = [];
var species;
var comparisontypeid;
var experiment;

// Setup Gene Filter
var minLaVal;
var minLaSlashVal;
var minLqVal;
var maxLdVal;
var points;

var filterTimer;
var filter_timer_on = 0;

var FILTER_MAX = "Max: ";
var FILTER_MIN = "Min: ";

function clearFactorSelections(){
   transFacs = [];
   studies = [];
}

function filterInputTimer(){
   updateGeneFilter();
   filter_timer_on = 0;
}

function setupGeneFilter(){
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
         
         $('input.filter').val("");
         
         var inc = 0;
         
         jQuery.each(points, function (i, val) {
            $('div.filter:eq(' + inc + ') input').attr("placeholder", val.min + " - " + val.max);
            inc++;
         });
         
         $('input.filter').removeAttr("disabled");
         
         $('input.filter').change(function() {
           if(filter_timer_on == 1){
              clearTimeout(filterTimer);
           }
           filterTimer = setTimeout("filterInputTimer()", 750);
           filter_timer_on = 1;
         });
      },
      'json'
   );
}

function updateGeneFilter(){ 
   minLaVal = $('#minla').val();
   minLaSlashVal = $('#minlaslash').val();
   minLqVal = $('#minlq').val();
   maxLdVal = $('#maxld').val();

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
   clearFactorSelections();
   updateFactorList(minLaVal, minLaSlashVal, minLqVal, maxLdVal, species, comparisontypeid, experiment);
}

function updateSpeciesList() {
   updateMultiselectList(speciesList, "species", "getSpeciesList", {},
    function () { 
       experimentList.fnClearTable();
       comparisonList.fnClearTable();
    },
    function (specs) {
       species = specs;
       updateComparisonList(specs);
       comparisontypeid = undefined;
       experiment = undefined;
       updateGeneFilter();
       fixAllTableWidths();
    });
}

function updateExperimentList(comparisontypeid) {
   updateMultiselectList(experimentList, "experimentid", "getExperimentList",
      { 'comparisontypeid': comparisontypeid },
      function () {},
      function (specs) {
         experiment = specs;
         updateGeneFilter();
         fixAllTableWidths();
      }
   );
}

function updateComparisonList(curSpecies) {
   updateMultiselectList(comparisonList, "comparisontypeid", "getComparisonList",
      { 'species': curSpecies },
      function () {},
      function (specs) {
         comparisontypeid = specs;
         updateExperimentList(specs);
         experiment = undefined;
         updateGeneFilter();
      }
   );
}

function updateFactorList(minLaVal, minLaSlashVal, minLqVal, maxLdVal, species, comparisontypeid, experiment) {
   geneFoundList.fnClearTable();
   comparisonFromGeneList.fnClearTable();
   updateMultiselectList(factorList, "transfac", "getDistinctFactorList",
      {
         'species' : species,
         'comparisontypeid' : comparisontypeid,
         'experiment' : experiment,
         'minLa' : minLaVal,
         'minLaSlash' : minLaSlashVal,
         'minLq' : minLqVal,
         'maxLd' : maxLdVal
      },
      function () {},
      function (specs) {
         updateGeneFoundList(minLaVal, minLaSlashVal, minLqVal, maxLdVal,
          species, comparisontypeid, experiment, specs);
         updateComparisonFromGeneList("");
      }
   );
}

function updateComparisonFromGeneList(genename) {
   jQuery.get("ajax/getComparisonFromGeneList",
      { 'genename' : genename },
      function(data) {
         comparisonFromGeneList.fnClearTable();
         comparisonFromGeneList.fnAddData(data);
         fixTableWidth(comparisonFromGeneList);
      }, 'json'
   );
}

function updateGeneFoundList(minLaVal, minLaSlashVal, minLqVal, maxLdVal,
 species, comparisontypeid, experiment, transFacs) {
   updateMultiselectList(geneFoundList, "genename", "getGeneFoundListFromDB",
      { 
         'transFacs' : transFacs,
         'species' : species,
         'comparisontypeid' : comparisontypeid,
         'experiment' : experiment,
         'minLa' : minLaVal,
         'minLaSlash' : minLaSlashVal,
         'minLq' : minLqVal,
         'maxLd' : maxLdVal
      },
      function () {
         comparisonFromGeneList.fnClearTable();
      },
      function (specs) {
         updateComparisonFromGeneList(specs);
      }
   );
}

function setupExperimentHierarchy() {
   var firstRowHeight = "100px";
   var secondRowHeight = "150px";
   var thirdRowHeight = "150px";

   comparisonFromGeneList = $('#comparisonFromGeneList').dataTable({
      "sDom": "<'row'<'span6'f>r>t<'row'<'span6'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "oLanguage": {
         "sSearch": "Search Comparisons"
      },
      "aoColumns": [
         {"sTitle": "Comparison", "mDataProp": "comparison"},
         {"sTitle": "Study", "mDataProp": "label"}/*
         {"sTitle": "ComparisonTypeId", "mDataProp": "comparisontypeid", "bVisible": false},
         {"sTitle": "Study", "mDataProp": "studyPretty"},
         {"sTitle": "StudyOrig", "mDataProp": "study", "bVisible": false}*/
      ]
   });

   factorList = $('#factorList').dataTable({
      "sDom": "<'row'<'span6'f>r>t<'row'<'span6'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "oLanguage": {
         "sSearch": "Search Transcription Factors"
      },
      "aoColumns": [
         {"sTitle": "Transfac", "mDataProp": "transfac"},
         {"sTitle": "Studies", "mDataProp": "numStudies"},
         {"sTitle": "Genes", "mDataProp": "numGenes"},
         {"sTitle": "Occurrences", "mDataProp": "numOccs"},
         {"sTitle": "AllRow", "mDataProp": "allRow", "bVisible": false}
      ],
      "aaSortingFixed": [[4, "desc"]]
   });
 
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

   geneFoundList = $('#geneFoundList').dataTable( {
      "sDom": "<'row'<'span6'f>r>t<'row'<'span6'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "oLanguage": {
         "sSearch": "Search Genes"
      },
      "aoColumns": [
         {"sTitle": "GeneName", "mDataProp": "genename"},
         {"sTitle": "Regulation", "mDataProp": "regulation"}
      ]
   });
   setupGeneFilter();
   updateFactorList();
   updateSpeciesList();
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(factorList);
}

$(window).load(function() {
   var regInput = Brovine.newRegInput("#regFilter", function() {
      geneFoundList.fnDraw();
   });

   setupExperimentHierarchy();
   setupPlaceholders();
   fixAllTableWidths();
   
   $.fn.dataTableExt.afnFiltering.push(regInput.filter("geneFoundList", 1));
});
