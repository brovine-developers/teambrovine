var transFacs = new Array();
var studies = new Array();
var species;
var comparisontypeid;
var experiment;

// Setup Gene Filter
var minLaVal;
var minLaSlashVal;
var minLqVal;
var maxLdVal;

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

   updateGeneFoundList(transFacs, studies, minLaVal, minLaSlashVal, minLqVal, maxLdVal, species, comparisontypeid, experiment);
}



function updateSpeciesList() {
   jQuery.get("ajax/getSpeciesList",
   function(data) {
      speciesList.fnClearTable();
      speciesList.fnAddData(data);

      speciesList.$('tr').click(function(e) {
         speciesList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = speciesList.fnGetData(this);
         species = rowData.species;
         experimentList.fnClearTable();
         updateComparisonList(species);
         comparisontypeid = null;
         experiment = null;
         updateGeneFilter();
         fixAllTableWidths();
      });
   },
   'json'
   );
}

function updateExperimentList(comparisontypeid) {
   jQuery.get("ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid
   },
   function(data) {
      experimentList.fnClearTable();
      experimentList.fnAddData(data);
      fixTableWidth(experimentList);
      experimentList.$('tr').click(function(e) {
         experimentList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = experimentList.fnGetData(this);
         experiment = rowData.label;
         updateGeneFilter();
         fixAllTableWidths();
      });
   },
   'json'
   );
}

function updateComparisonList(curSpecies) {
   jQuery.get("ajax/getComparisonList",
      {
         'species': curSpecies
      },
      function(data) {
         comparisonList.fnClearTable();
         comparisonList.fnAddData(data);

         comparisonList.$('tr').click(function(e) {
            comparisonList.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = comparisonList.fnGetData(this);
            comparisontypeid = rowData.comparisontypeid;
            updateExperimentList(comparisontypeid);
            experiment = null;
            updateGeneFilter();
         });
      },
      'json'
   );

}

function updateFactorList() {
   jQuery.get("ajax/getDistinctFactorList",
   function(data) {
      factorList.fnClearTable();
      factorList.fnAddData(data);
      fixTableWidth(factorList);
      factorList.$('tr').click(function(e) {
         if(e.metaKey|| e.ctrlKey){
	    $(this).addClass('selected');
            var rowData = factorList.fnGetData(this);
            transFacs.push(rowData.transfac);
            studies.push(rowData.study);
	 }
         else{
            factorList.$('tr').removeClass('selected');
            $(this).addClass('selected');
          
            transFacs.length = 0;
            studies.length = 0;
          
            var rowData = factorList.fnGetData(this);
            transFacs.push(rowData.transfac);
            studies.push(rowData.study);
         }
         updateGeneFilter();
         updateComparisonFromGeneList("");
      });

   },
   'json'
   );
}

function updateComparisonFromGeneList(genename) {
   jQuery.get("ajax/getComparisonFromGeneList",
      {
         'genename' : genename
      },
      function(data) {
         comparisonFromGeneList.fnClearTable();
         comparisonFromGeneList.fnAddData(data);
         fixTableWidth(comparisonFromGeneList);
      },
      'json'
   );
}

function updateGeneFoundList(transFacs, studies, minLaVal, minLaSlashVal, minLqVal, maxLdVal, species, comparisontypeid, experiment) {
   jQuery.get("ajax/getGeneFoundListFromDB",
      {
         'transFacs' : transFacs,
         'studies' : studies,
         'minLa' : minLaVal,
         'minLaSlash' : minLaSlashVal,
         'minLq' : minLqVal,
         'maxLd' : maxLdVal,
         'species' : species,
         'comparisontypeid' : comparisontypeid,
         'experiment' : experiment
      },
      function(data) {
         geneFoundList.fnClearTable();
         geneFoundList.fnAddData(data);
         fixTableWidth(geneFoundList);
         geneFoundList.$('tr').click(function(e) {
            geneFoundList.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = geneFoundList.fnGetData(this);
            var genename = rowData.genename;
            updateComparisonFromGeneList(genename);
         });
      },
      'json'
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
      "aoColumns": [
         {"sTitle": "Comparison", "mDataProp": "comparison"},
         {"sTitle": "Study", "mDataProp": "label"}/*
         {"sTitle": "ComparisonTypeId", "mDataProp": "comparisontypeid", "bVisible": false},
         {"sTitle": "Study", "mDataProp": "studyPretty"},
         {"sTitle": "StudyOrig", "mDataProp": "study", "bVisible": false}*/
      ]
   });

   factorList = $('#factorList').dataTable({
      "sDom": "<'row'<'span4'f>r>t<'row'<'span4'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "oLanguage": {
         "sSearch": "Search Transcription Factors"
      },
      "aoColumns": [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "Study", "mDataProp": "studyPretty"},
         {"sTitle": "#", "mDataProp": "numTimes"},
         {"sTitle": "AllRow", "mDataProp": "allRow", "bVisible": false},
         {"sTitle": "StudyOrig", "mDataProp": "study", "bVisible": false}
      ],
      "aaSortingFixed": [[3,'desc']]

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
         {"sTitle": "Genes", "mDataProp": "genecount_all"},
         {"sTitle": "Up", "mDataProp": "genecount_up"},
         {"sTitle": "Down", "mDataProp": "genecount_down"},
         {"sTitle": "Experimentid", "mDataProp": "experimentid", "bVisible": false}
      ]
   });

   geneFoundList = $('#geneFoundList').dataTable( {
      "sDom": "<'row'<'span4'f>r>t<'row'<'span4'i>>",
      "sPaginationType": "bootstrap",
      "bPaginate": false,
      "bInfo": false,
      "sScrollY": thirdRowHeight,
      "oLanguage": {
         "sSearch": "Search Genes"
      },
      "aoColumns": [
         {"sTitle": "GeneName", "mDataProp": "genename"},
         {"sTitle": "Regulation", "mDataProp": "regulation"}
      ]
   });

   updateFactorList();
   updateSpeciesList();
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(factorList);
}

$(document).ready(function() {
   setupExperimentHierarchy();
   setupPlaceholders();
   fixAllTableWidths();
});

// Work around for chrome. Sometimes, it doesn't properly fix tables.
$(window).load(function() {
   fixAllTableWidths();
});
