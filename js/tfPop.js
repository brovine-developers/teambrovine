var curGeneid;
var experiment;

function updateSpeciesList() {
   jQuery.get("ajax/getSpeciesList",
   function(data) {
      speciesList.fnClearTable();
      speciesList.fnAddData(data);
      
      speciesList.$('tr').click(function(e) {
         var specs = new Array();
         $(this).toggleClass('selected');
         
         speciesList.$('tr.selected').each(function(i) {
            var rowData = speciesList.fnGetData(this);
            specs[i] = rowData.species;
         });

         experimentList.fnClearTable();
         tfSummary.fnClearTable();
         tfOccur.fnClearTable();
         
         if (specs.length > 0) {
            $('#sequenceInfo').empty();
            updateComparisonList(specs);
            fixAllTableWidths();
         }
      });
   },
   'json'
   );
}

function updateTFSummary(experimentid, la, la_slash, lq, ld) {
   jQuery.get("ajax/getTFDrillSummary",
      {
         "experimentid" : experimentid,
         "la" : la,
         "la_s" : la_slash,
         "lq" : lq,
         "ld" : ld
      },
      function(data) {
         tfSummary.fnClearTable();
         tfSummary.fnAddData(data);
         fixTableWidth(tfSummary);

         tfSummary.$('tr').click(function(e) {
            tfSummary.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = tfSummary.fnGetData(this);

            var tf = rowData.transfac;
            updateTFOccurrences(tf);
         });
      },
      'json'
   );
}

function updateTFOccurrences(tf) {
   jQuery.get("ajax/getTFOccur",
      {
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

function updateExperimentList(comparisontypeid) {
   jQuery.get("ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid
   },
   function(data) {
      experimentList.fnClearTable();
      $('#sequenceInfo').empty();
      experimentList.fnAddData(data);
      fixTableWidth(experimentList);
      experimentList.$('tr').click(function(e) {
         exps = new Array();
         $(this).toggleClass('selected');
         
         experimentList.$('tr.selected').each(function(i) {
            var rowData = experimentList.fnGetData(this);
            exps[i] = rowData.experimentid;
         });

         if (exps.length > 0) {
            updateTF();
         }
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
            var comps = new Array();
            $(this).toggleClass('selected');
         
            comparisonList.$('tr.selected').each(function(i) {
               var rowData = comparisonList.fnGetData(this);
               comps[i] = rowData.comparisontypeid;
            });

            tfSummary.fnClearTable();
            tfOccur.fnClearTable();
            
            if (comps.length > 0) {
               $('#sequenceInfo').empty();
               updateExperimentList(comps);
               fixAllTableWidths();
            }
         });
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
         {"sTitle": "Genes", "mDataProp": "genecount_all"},
         {"sTitle": "Up", "mDataProp": "genecount_up"},
         {"sTitle": "Down", "mDataProp": "genecount_down"},
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
         {"sTitle": "Occurrences", "mDataProp": "numOccs"},
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
         {"sTitle": "Study", "mDataProp": "study"},        
         {"sTitle": "Beginning", "mDataProp": "beginning"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "Length", "mDataProp": "length"},
      ]
      
   });

   
   $("#minla").keyup(function () {
      updateTF();
   });
   
   $("#minlaslash").keyup(function () {
      updateTF();
   });
   
   $("#minlq").keyup(function () {
      updateTF();
   });
   
   $("#maxld").keyup(function () {
      updateTF();
   });
   
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
   
   updateTFSummary(exps, la, la_s, lq, ld);
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(tfSummary);
   fixTableWidth(tfOccur);
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
