var curGeneid;
var geneModal;
var factorModal;
var sequenceModal;

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

function setupEditAndDelete() {

   // Gene Modal //////////////////////////////////////////////////////////////

   geneModal = $('#editGeneModal').modal({
      'show': false
   });

   $('#editGene').click(function() {
      if (!$(this).hasClass('disabled')) {
         var geneData = geneList.fnGetData(geneList.$('.selected')[0]);
         $('#genenameInput').val(geneData.genename);
         $('#geneabbrevInput').val(geneData.geneabbrev);
         $('#genechromosomeInput').val(geneData.chromosome);
         $('#genestartInput').val(geneData.start);
         $('#geneendInput').val(geneData.end);
         $('#geneidInput').val(geneData.geneid);
         $('#geneLastEdited').html(geneData.date_edited_pretty);

         var selectedInput = '#generegulationInputUp';
         if (geneData.regulation == 'down') {
            selectedInput = '#generegulationInputDown';
         }

         $(selectedInput).prop('checked', true);
         geneModal.modal('show');
      }
   });

   $('#editGeneSave').click(function() {
      // Save gene info here.

      var geneRegulation = 'up';
      if ($('#generegulationInputDown').prop('checked')) {
         geneRegulation = 'down';
      }

      var row = geneList.$('.selected')[0];
      var oldGeneData = geneList.fnGetData(row);
      // Make a new geneData object for the row. 
      var newGeneData = $.extend({}, oldGeneData, {
         geneid: $('#geneidInput').val(),
         genename: $('#genenameInput').val(),
         geneabbrev: $('#geneabbrevInput').val(),
         chromosome: $('#genechromosomeInput').val(),
         start: $('#genestartInput').val(),
         end: $('#geneendInput').val(),
         regulation: geneRegulation,
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime()
      });

      geneList.fnUpdate(newGeneData, row);
      geneModal.modal('hide');

      var comparisonRow = comparisonList.$('tr.selected')[0];
      var compData = comparisonList.fnGetData(comparisonRow);
      var serverData = $.extend({}, newGeneData, {
         comparisontypeid: compData.comparisontypeid
      });

      jQuery.post('ajax/updateGene', serverData, function(experimentData) {
         // Update experiment data on save. The numbers might change.
         updateExperimentListData(experimentData);
         var matchData = {
            experimentid: oldGeneData.experimentid
         };
         selectTableRow(experimentList, matchData);
      }, 'json');
   });

   // Comparison Modal ////////////////////////////////////////////////////////
   
   comparisonModal = $('#editComparisonModal').modal({
      'show': false
   });

   $('#editComparison').click(function() {
      if (!$(this).hasClass('disabled')) {
         var comparisonData = comparisonList.fnGetData(comparisonList.$('.selected')[0]);
         $('#comparisonCelltypeInput').val(comparisonData.celltype);
         $('#comparisonSpeciesInput').val(comparisonData.species);
         $('#comparisontypeidInput').val(comparisonData.comparisontypeid);
         $('#comparisonLastEdited').html(comparisonData.date_edited_pretty);

         comparisonModal.modal('show');
      }
   });

   $('#editComparisonSave').click(function() {
      // Save comparison info here.

      var row = comparisonList.$('.selected')[0];
      var oldComparisonData = comparisonList.fnGetData(row);
      // Make a new comparisonData object for the row. 
      var newComparisonData = {
         comparisontypeid: $('#comparisontypeidInput').val(),
         celltype: $('#comparisonCelltypeInput').val(),
         species: $('#comparisonSpeciesInput').val(),
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime()
      };

      newComparisonData.comparison = 
       newComparisonData.species.charAt(0).toUpperCase() +
       newComparisonData.species.substr(1) + 
       ": " +
       newComparisonData.celltype;

      comparisonList.fnUpdate(newComparisonData, row);
      comparisonModal.modal('hide');

      jQuery.post('ajax/updateComparison', newComparisonData, function(data) {
         // Update species list on update.
         updateSpeciesListData(data);
         matchData = {
            'species': newComparisonData.species
         };
         selectTableRow(speciesList, matchData);
      }, 'json');
   });
   
   // Experiment Modal ////////////////////////////////////////////////////////
   
   experimentModal = $('#editExperimentModal').modal({
      'show': false
   });

   $('#editExperiment').click(function() {
      if (!$(this).hasClass('disabled')) {
         var experimentData = experimentList.fnGetData(experimentList.$('.selected')[0]);
         $('#experimentLabelInput').val(experimentData.label);
         $('#experimentidInput').val(experimentData.experimentid);
         $('#experimentLastEdited').html(experimentData.date_edited_pretty);

         experimentModal.modal('show');
      }
   });

   $('#editExperimentSave').click(function() {
      // Save experiment info here.

      var row = experimentList.$('.selected')[0];
      var oldExperimentData = experimentList.fnGetData(row);
      // Make a new experimentData object for the row. 


      var newExperimentData = $.extend(
       {}, oldExperimentData, {
         label: $('#experimentLabelInput').val(),
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime(),
      });

      experimentList.fnUpdate(newExperimentData, row);
      experimentModal.modal('hide');

      jQuery.post('ajax/updateExperiment', newExperimentData);
   });
   
   // Sequence Modal ////////////////////////////////////////////////////////
   
   sequenceModal = $('#editSequenceModal').modal({
      'show': false
   });

   $('#editSequence').click(function() {
      if (!$(this).hasClass('disabled')) {
         var sequenceData = sequenceList.fnGetData(sequenceList.$('.selected')[0]);
         $('#sequenceBeginningInput').val(sequenceData.beginning);
         $('#sequenceLengthInput').val(sequenceData.length);
         $('#sequenceSenseInput').val(sequenceData.sense);
         $('#seqidInput').val(sequenceData.seqid);
         $('#sequenceLastEdited').html(sequenceData.date_edited_pretty);

         sequenceModal.modal('show');
      }
   });

   $('#editSequenceSave').click(function() {
      // Save sequence info here.

      var row = sequenceList.$('.selected')[0];
      var oldSequenceData = sequenceList.fnGetData(row);
      // Make a new sequenceData object for the row. 


      var newSequenceData = $.extend(
       {}, oldSequenceData, {
         beginning: $('#sequenceBeginningInput').val(),
         length: $('#sequenceLengthInput').val(),
         sense: $('#sequenceSenseInput').val(),
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime(),
      });

      sequenceList.fnUpdate(newSequenceData, row);
      sequenceModal.modal('hide');

      jQuery.post('ajax/updateSequence', newSequenceData, function(sequenceInfoData) {
         updateSequenceInfoData(sequenceInfoData);
         newSequenceData.sequence = sequenceInfoData.sequenceInfo.sequence;
         sequenceList.fnUpdate(newSequenceData, row);

      }, 'json');
   });
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
   jQuery.get("ajax/getSpeciesList",
   function(data) {
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');
      $('#editComparison').addClass('disabled');
      $('#hideComparison').addClass('disabled');
      $('#editExperiment').addClass('disabled');
      $('#hideExperiment').addClass('disabled');

      updateSpeciesListData(data);
   },
   'json'
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

   $("#sequenceInfo").removeClass("hidden");
   fixTableWidth(similarList);
   fixTableWidth(matchList);

}

function updateSequenceInfo(seqid) {
   jQuery.get("ajax/getSequenceInfo",
   {
      'seqid': seqid
   },
   function(data) {
      updateSequenceInfoData(data);
   }, 'json');
}

function updateSequenceList(geneid, transfac, study) {
   jQuery.get("ajax/getSequenceList",
   {
      'geneid': geneid,
      'transfac': transfac,
      'study': study
   },
   function(data) {
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

      sequenceList.fnClearTable();
      $('#sequenceInfo').addClass('hidden');
      sequenceList.fnAddData(data);
      fixTableWidth(sequenceList);

      sequenceList.$('tr').click(function() {
         sequenceList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         $('#editSequence').removeClass('disabled');
         $('#hideSequence').removeClass('disabled');
         var rowData = sequenceList.fnGetData(this);

         updateSequenceInfo(rowData.seqid);
      });
   },
   'json'
   );
}

function updateFactorList() {
   jQuery.get("ajax/getFactorList",
   { 'geneid': curGeneid },
   function(data) {
      factorList.fnClearTable();
      sequenceList.fnClearTable();
      $('#sequenceInfo').addClass('hidden');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

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
      $('#sequenceInfo').addClass('hidden');
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

      geneList.fnAddData(data);
      fixTableWidth(geneList);
      geneList.$('tr').click(function(e) {
         geneList.$('tr').removeClass('selected');
         $(this).addClass('selected');
         var rowData = geneList.fnGetData(this);
         curGeneid = rowData.geneid;
         updateFactorList();
         $('#editGene').removeClass('disabled');
         $('#hideGene').removeClass('disabled');
      });

   },
   'json'
   );
}

function updateExperimentListData(data) {
   experimentList.fnClearTable();
   experimentList.fnAddData(data);
   fixTableWidth(experimentList);
   experimentList.$('tr').click(function(e) {
      experimentList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      $('#editExperiment').removeClass('disabled');
      $('#hideExperiment').removeClass('disabled');

      var rowData = experimentList.fnGetData(this);
      var experimentid = rowData.experimentid;
      updateGeneList(experimentid);
   });
}


function updateExperimentList(comparisontypeid) {
   jQuery.get("ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid
   },
   function(data) {
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');
      $('#editExperiment').addClass('disabled');
      $('#hideExperiment').addClass('disabled');

      $('#sequenceInfo').addClass('hidden');
      
      updateExperimentListData(data);
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
         $('#editGene').addClass('disabled');
         $('#hideGene').addClass('disabled');
         $('#editSequence').addClass('disabled');
         $('#hideSequence').addClass('disabled');
         $('#editComparison').addClass('disabled');
         $('#hideComparison').addClass('disabled');
         $('#editExperiment').addClass('disabled');
         $('#hideExperiment').addClass('disabled');

         comparisonList.fnClearTable();
         comparisonList.fnAddData(data);
         
         comparisonList.$('tr').click(function(e) {
            comparisonList.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = comparisonList.fnGetData(this);
            
            $('#editComparison').removeClass('disabled');
            $('#hideComparison').removeClass('disabled');

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
         {"sTitle": "Geneid", "mDataProp": "geneid", "bVisible": false},
         {"sTitle": "GeneName", "mDataProp": "genename", "bVisible": false}
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
         {"sTitle": "La", "mDataProp": "la", "sType": "numeric"},
         {"sTitle": "La/", "mDataProp": "la_slash", "sType": "numeric"},
         {"sTitle": "Lq", "mDataProp": "lq", "sType": "numeric"},
         {"sTitle": "Ld", "mDataProp": "ld", "sType": "numeric"},
         {"sTitle": "Lpv", "mDataProp": "lpv", "sType": "numeric"},
         /*
         {"sTitle": "Sc", "mDataProp": "sc", "sType": "numeric"},
         {"sTitle": "Sm", "mDataProp": "sm", "sType": "numeric"},
         {"sTitle": "Spv", "mDataProp": "spv", "sType": "numeric"},
         {"sTitle": "Ppv", "mDataProp": "ppv", "sType": "numeric"},
         */
         {"sTitle": "Sequence", "mDataProp": "sequence"},
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "Study", "mDataProp": "studyPretty"},
         {"sTitle": "Sequenceid", "mDataProp": "seqid", "bVisible": false}
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

   // Setup Gene filter
   var geneFilterVal = $("#geneFilterOptions input[type='radio']:checked").val();

   var filterGeneList = function(oSettings, aData, iDataIndex) {
      if (oSettings.sTableId != "geneList") {
         return true;
      }

      // aData[4] is regulation.
      return (geneFilterVal == 'all' || geneFilterVal == aData[4]);
   };

   $.fn.dataTableExt.afnFiltering.push(filterGeneList);

   // Add radio button listener to redraw table.
   $('#geneFilterOptions input').change(function() {
      // Find button val outside the loop for speed purposes.
      geneFilterVal = $("#geneFilterOptions input[type='radio']:checked").val();
      geneList.fnDraw();
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
         aData[0] >= minBegVal && 
         aData[0] <= maxBegVal && 
         // Check Sense
         (aData[2] == senseFilterVal || senseFilterVal == 'all') &&
         // Check L-Values
         aData[3] >= minLaVal &&
         aData[4] >= minLaSlashVal &&
         aData[5] >= minLqVal &&
         aData[6] <= maxLdVal
      );
   };

   $.fn.dataTableExt.afnFiltering.push(filterSequenceList);

   var triggerSequenceListRedraw = function() {
      updateSequenceFilter();
      sequenceList.fnDraw();

   };

   // Add radio button listener to redraw table.
   $('#senseFilters input').change(triggerSequenceListRedraw);
   $("#sequenceFilterOptions input[type='text']").keyup(triggerSequenceListRedraw);

   // Setup edit / delete listeners and modals.
   setupEditAndDelete();

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

// Work around for chrome. Sometimes, it doesn't properly fix tables.
$(window).load(function() {
   fixAllTableWidths();
});
