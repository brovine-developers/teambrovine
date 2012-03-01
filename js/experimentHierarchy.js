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
         geneList.fnClearTable();
         factorList.fnClearTable();
         sequenceList.fnClearTable();
         $('#sequenceInfo').empty();
         updateComparisonList(curSpecies);
         fixAllTableWidths();
      });
   },
   'json'
   );
}

function updateSequenceInfo(seqid) {
   $('#sequenceInfo').load("ajax/getSequenceInfo",
   {
      'seqid': seqid
   });
}

function updateSequenceList(geneid, transfac, study) {
   jQuery.get("ajax/getSequenceList",
   {
      'geneid': geneid,
      'transfac': transfac,
      'study': study
   },
   function(data) {
      sequenceList.fnClearTable();
      $('#sequenceInfo').empty();
      sequenceList.fnAddData(data);
      fixTableWidth(sequenceList);

      sequenceList.$('tr').click(function() {
         sequenceList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = sequenceList.fnGetData(this);

         updateSequenceInfo(rowData.seqid);
      });
   },
   'json'
   );
}

function updateFactorList(geneid) {
   jQuery.get("ajax/getFactorList",
   { 'geneid': geneid },
   function(data) {
      factorList.fnClearTable();
      sequenceList.fnClearTable();
      $('#sequenceInfo').empty();
      factorList.fnAddData(data);
      fixTableWidth(factorList);
      factorList.$('tr').click(function(e) {
         factorList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = factorList.fnGetData(this);
         var transfac = rowData.transfac;
         var study = rowData.study;
         var geneid = geneList.fnGetData(geneList.$('tr.selected')[0]).geneid;
         updateSequenceList(geneid, transfac, study);
      });

   },
   'json'
   );
}

function updateGeneList(experimentid) {
   jQuery.get("ajax/getGeneList",
   { 'experimentid': experimentid },
   function(data) {
      geneList.fnClearTable();
      sequenceList.fnClearTable();
      factorList.fnClearTable();
      $('#sequenceInfo').empty();

      geneList.fnAddData(data);
      fixTableWidth(geneList);
      geneList.$('tr').click(function(e) {
         geneList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = geneList.fnGetData(this);
         var geneid = rowData.geneid;
         updateFactorList(geneid);
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
      $('#sequenceInfo').empty();
      experimentList.fnAddData(data);
      fixTableWidth(experimentList);
      experimentList.$('tr').click(function(e) {
         experimentList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = experimentList.fnGetData(this);
         var experimentid = rowData.experimentid;
         updateGeneList(experimentid);
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
            geneList.fnClearTable();
            factorList.fnClearTable();
            sequenceList.fnClearTable();
            updateExperimentList(comparisonTypeId);
         });
      },
      'json'
   );
   
}

function setupExperimentHierarchy() {
   var firstRowHeight = "100px";
   var secondRowHeight = "100px";
   var thirdRowHeight = "100px";
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

   geneList = $('#geneList').dataTable({
      "sDom": "<'row'<'span8'f>r>t<'row'<'span3'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Genes",
         "sInfo": "Showing _TOTAL_ genes",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": secondRowHeight,
      "aoColumns": [
         {"sTitle": "Gene", "mDataProp": "geneabbrev"},
         {"sTitle": "Chr", "mDataProp": "chromosome"},
         {"sTitle": "Start", "mDataProp": "start"},
         {"sTitle": "End", "mDataProp": "end"},
         {"sTitle": "Reg", "mDataProp": "regulation"},
         {"sTitle": "Factors", "mDataProp": "numFactors"},
         {"sTitle": "Geneid", "mDataProp": "geneid", "bVisible": false}
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
         {"sTitle": "Study", "mDataProp": "study"},
         {"sTitle": "#", "mDataProp": "numTimes"}
      ]
   
   });
   
   sequenceList = $('#sequenceList').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "sScrollY": thirdRowHeight,
      "oLanguage": {
         "sSearch": "Search Regulatory Sequences"
      },
      "aoColumns": [
         {"sTitle": "Begin", "mDataProp": "beginning"},
         {"sTitle": "Length", "mDataProp": "length"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "La", "mDataProp": "la"},
         {"sTitle": "La/", "mDataProp": "la_slash"},
         {"sTitle": "Lq", "mDataProp": "lq"},
         {"sTitle": "Ld", "mDataProp": "ld"},
         {"sTitle": "Lpv", "mDataProp": "lpv"},
         {"sTitle": "Sc", "mDataProp": "sc"},
         {"sTitle": "Sm", "mDataProp": "sm"},
         {"sTitle": "Spv", "mDataProp": "spv"},
         {"sTitle": "Ppv", "mDataProp": "ppv"},
         {"sTitle": "Sequence", "mDataProp": "sequence"},
         {"sTitle": "Sequenceid", "mDataProp": "seqid", "bVisible": false}
      ]
   
   });

   // Get the list of species from the server.
   updateSpeciesList();
}

function fixAllTableWidths() {
   fixTableWidth(speciesList);
   fixTableWidth(experimentList);
   fixTableWidth(geneList);
   fixTableWidth(factorList);
   fixTableWidth(sequenceList);
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
