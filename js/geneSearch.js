var curGeneid;

function updateSpeciesList() {
   jQuery.get("ajax/getSpeciesList",
   function(data) {
      speciesList.fnClearTable();
      speciesList.fnAddData(data);

      speciesList.$('tr').click(function(e) {
         speciesList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = speciesList.fnGetData(this);

         var curSpecies = rowData.species;
         experimentList.fnClearTable();
         updateComparisonList(curSpecies);
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
         var experimentid = rowData.experimentid;
         //do something with this data
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
            var comparisonTypeId = rowData.comparisontypeid;
            updateExperimentList(comparisonTypeId);
         });
      },
      'json'
   );

}

function updateFactorList() {
   jQuery.get("ajax/getDistinctFactorList",
   function(data) {
      factorList.fnClearTable();
     // sequenceList.fnClearTable();
      $('#sequenceInfo').empty();
      factorList.fnAddData(data);
      fixTableWidth(factorList);
      factorList.$('tr').click(function(e) {
         factorList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = factorList.fnGetData(this);
         var transfac = rowData.transfac;
         var study = rowData.study;
         //updateSequenceList(curGeneid, transfac, study);
      });

   },
   'json'
   );
}

function setupExperimentHierarchy() {
   var firstRowHeight = "100px";
   var secondRowHeight = "150px";
   var thirdRowHeight = "150px";
  
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
