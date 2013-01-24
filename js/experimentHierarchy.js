var curGeneid;
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

function updateHideButtonText(node, rowHidden) {
   var span = $('span', node);
   var text = span.html();
   if (rowHidden == 1) {
      span.html(text.replace("Hide", "Show"));
   } else {
      span.html(text.replace("Show", "Hide"));
   }
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

function colorDeletedAndEdited(nRow, aData) {
   if (aData.hidden == "1") {
      $(nRow).addClass('hiddenRow');
   } 
   else if (aData.date_edited > 0) {
      $(nRow).addClass('editedRow');
   }
}

function setupDelete() {
   var tablesByHideId = {
      hideComparison: comparisonList,
      hideExperiment: experimentList,
      hideGene: geneList,
      hideSequence: sequenceList,
      hideMatch: matchList
   };

   var primaryKeysByHideId = {
      hideComparison: 'comparisontypeid',
      hideExperiment: 'experimentid',
      hideGene: 'geneid',
      hideSequence: 'seqid',
      hideMatch: 'matchid'
   }

   $('.hideButton').click(function() {
      if (!$(this).hasClass('disabled')) {
         // Send an ajax request to hide the element and refresh the page.
         var id = $(this).attr('id');
         var rowData = getSelectedRowData(tablesByHideId[id]);
         var key = primaryKeysByHideId[id];
         var value = rowData[key];
         var serverData = {
            field: key,
            value: value,
            isHidden: rowData.hidden
         };

         jQuery.post('edit/toggleRow', serverData, function(responseData) {
            location.reload(true);
         }, 'json');
      }
   });
}

function getPromoter(geneid) {
   jQuery.get("ajax/getPromoter", {
      geneid: geneid
   },
   function(data) {
      $('#genepromoterInput').removeClass('disabled').html(data.promoter);
   }, 'json');

}

function setupEdit() {
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
         $('#genepromoterInput').addClass('disabled');

         getPromoter(geneData.geneid);

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
         comparisontypeid: compData.comparisontypeid,
         promoter: $('#genepromoterInput').val(),
         showHidden: showHidden
      });

      jQuery.post('edit/gene', serverData, function(experimentData) {
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

      var serverData = $.extend({}, newComparisonData, {
         showHidden: showHidden
      });

      jQuery.post('edit/comparison', serverData, function(data) {
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

      var serverData = $.extend({}, newExperimentData, objShowHidden);

      jQuery.post('edit/experiment', serverData);
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

      var serverData = $.extend({}, newSequenceData, objShowHidden);

      jQuery.post('edit/sequence', serverData, function(sequenceInfoData) {
         updateSequenceInfoData(sequenceInfoData);
         newSequenceData.sequence = sequenceInfoData.sequenceInfo.sequence;
         sequenceList.fnUpdate(newSequenceData, row);

      }, 'json');
   });
   
   // Match Modal ////////////////////////////////////////////////////////
   
   matchModal = $('#editMatchModal').modal({
      'show': false
   });

   $('#editMatch').click(function() {
      if (!$(this).hasClass('disabled')) {
         var matchData = matchList.fnGetData(matchList.$('.selected')[0]);
         $('#matchStudyInput').val(matchData.study);
         $('#matchTransfacInput').val(matchData.transfac);
         $('#matchLaInput').val(matchData.la);
         $('#matchLaSlashInput').val(matchData.la_slash);
         $('#matchLqInput').val(matchData.lq);
         $('#matchLdInput').val(matchData.ld);
         $('#matchLpvInput').val(matchData.lpv);
         $('#matchScInput').val(matchData.sc);
         $('#matchSmInput').val(matchData.sm);
         $('#matchSpvInput').val(matchData.spv);
         $('#matchPpvInput').val(matchData.ppv);
         $('#matchidInput').val(matchData.seqid);
         $('#matchLastEdited').html(matchData.date_edited_pretty);

         matchModal.modal('show');
      }
   });

   $('#editMatchSave').click(function() {
      // Save match info here.

      var row = matchList.$('tr.selected')[0];
      var oldMatchData = matchList.fnGetData(row);
      // Make a new matchData object for the row. 


      var newMatchData = $.extend(
       {}, oldMatchData, {
         study: $('#matchStudyInput').val(),
         studyPretty: $('#matchStudyInput').val().replace('/', ' /<br>'),
         transfac: $('#matchTransfacInput').val(),
         la: $('#matchLaInput').val(),
         la_slash: $('#matchLaSlashInput').val(),
         lq: $('#matchLqInput').val(),
         ld: $('#matchLdInput').val(),
         lpv: $('#matchLpvInput').val(),
         sc: $('#matchScInput').val(),
         sm: $('#matchSmInput').val(),
         spv: $('#matchSpvInput').val(),
         ppv: $('#matchPpvInput').val(),
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime(),
      });

      matchList.fnUpdate(newMatchData, row);
      matchModal.modal('hide');

      var selectedGeneid = getSelectedRowData(geneList).geneid;
      var selectedFactorData = getSelectedRowData(factorList);
      var selectedSeqid = getSelectedRowData(sequenceList).seqid;

      var selectedExperimentid = getSelectedRowData(experimentList).experimentid;

      var serverData = $.extend({}, newMatchData, {
         selectedExperimentid: selectedExperimentid,
         selectedGeneid: selectedGeneid,
         allRowSelected: selectedFactorData.allRow,
         showHidden: showHidden
      });

      jQuery.post('edit/match', serverData, function(data) {
         updateGeneListData(data.geneData);
         selectTableRow(geneList, { geneid: selectedGeneid });
         
         updateSequenceListData(data.sequenceData);
         selectTableRow(sequenceList, { seqid: selectedSeqid });
         
         updateFactorListData(data.factorData);
         if (selectedFactorData.allRow) {
            selectTableRow(factorList, { 
               allRow: 1 
            });
         } else {
            selectTableRow(factorList, { 
               transfac: newMatchData.transfac,
               study: newMatchData.study
            });
         }
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
   { showHidden: showHidden },
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

   matchList.$('tr').click(function(e) {
      matchList.$('tr').removeClass('selected');
      $(this).addClass('selected');
      var rowData = matchList.fnGetData(this);

      var matchid = rowData.matchid;
      $('#editMatch').removeClass('disabled');
      $('#hideMatch').removeClass('disabled');
      updateHideButtonText($('#hideMatch'), rowData.hidden);

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
      $('#editSequence').removeClass('disabled');
      $('#hideSequence').removeClass('disabled');
      var rowData = sequenceList.fnGetData(this);
      updateHideButtonText($('#hideSequence'), rowData.hidden);

      updateSequenceInfo(rowData.seqid);
   });
}

function updateSequenceList(geneid, transfac, study) {
   jQuery.get("ajax/getSequenceList",
   {
      'geneid': geneid,
      'transfac': transfac,
      'study': study,
      'showHidden': showHidden
   },
   function(data) {
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');
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

function updateFactorList(exp) {
   jQuery.get("ajax/getFactorList",
   { 
      'geneid': curGeneid,
      'expid': exp,
      'showHidden': showHidden
   },
   function(data) {
      sequenceList.fnClearTable();
      $('#sequenceInfo').addClass('hidden');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

      updateFactorListData(data);
   },
   'json'
   );
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
      $('#editGene').removeClass('disabled');
      $('#hideGene').removeClass('disabled');
      updateHideButtonText($('#hideGene'), rowData.hidden);
   });
}

function updateGeneList(experimentid) {
   jQuery.get("ajax/getGeneList",
   { 
      'experimentid': experimentid,
      'showHidden': showHidden
   },
   function(data) {
      sequenceList.fnClearTable();
      factorList.fnClearTable();
      $('#sequenceInfo').addClass('hidden');
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

      updateGeneListData(data);
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
      updateHideButtonText($('#hideExperiment'), rowData.hidden);
      var experimentid = rowData.experimentid;
      updateGeneList(experimentid);
   });
}


function updateExperimentList(comparisontypeid) {
   jQuery.get("ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid,
      'showHidden': showHidden
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
         'species': curSpecies,
         'showHidden': showHidden
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
            updateHideButtonText($('#hideComparison'), rowData.hidden);

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
      "fnRowCallback": colorDeletedAndEdited,
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
      "fnRowCallback": colorDeletedAndEdited,
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
      "fnRowCallback": colorDeletedAndEdited,
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
      {"sTitle": "Chr", "mDataProp": "chromosome"},
      {"sTitle": "Start", "mDataProp": "start"},
      {"sTitle": "End", "mDataProp": "end"},
      {"sTitle": "Reg", "mDataProp": "regulation"},
      {"sTitle": "Factors", "mDataProp": "numFactors"},
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
      "fnRowCallback": colorDeletedAndEdited,
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

   factorCols = [
      {"sTitle": "Factor", "mDataProp": "transfac"},
      {"sTitle": "(#) Occurs", "mDataProp": "numTimes"}
   ];
   
   factorList = $('#factorList').dataTable({
      "sDom": "<'row'<'span4'f>r>t<'row'<'span4'i>>",
      "bPaginate": false,
      "sScrollY": secondRowHeight,
      "fnRowCallback": colorDeletedAndEdited,
      "oLanguage": {
         "sSearch": "Search Transcription Factors"
      },
      "aoColumns": [
         {"sTitle": "Factor", "mDataProp": "transfac"},
         {"sTitle": "(#) Occurs", "mDataProp": "numTimes"},
         {"sTitle": "AllRow", "mDataProp": "allRow", "bVisible": false}
      ],
      "aaSortingFixed": [[2,'desc']]
   });
   
   sequenceList = $('#sequenceList').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "sScrollY": thirdRowHeight,
      "fnRowCallback": colorDeletedAndEdited,
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
      "fnRowCallback": colorDeletedAndEdited,
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
      "fnRowCallback": colorDeletedAndEdited,
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

   // Setup edit / delete listeners and modals.
   setupEdit();
   setupDelete();

   showHidden = $("#showHidden").prop('checked') ? 1 : 0;
   objShowHidden = { showHidden: showHidden };

   $("#showHidden").change(function() {
      $('#showHiddenForm').submit();
   });

   $("#colorRows").change(function() {
      if ($(this).prop('checked')) {
         $('#content').addClass('coloredRows');
      } else {
         $('#content').removeClass('coloredRows');
      }
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
   }

   var headerCreate = function (objs) {
      var ans = "";

      for (key in objs)
         ans += objs[key].mDataProp + ", ";

      return ans + "\n";
   }

   $("#geneExport").localDownload({
      "func": function () {
         var headers = headerCreate(geneCols);
         var data = dataToCsv(geneList.fnGetData(), geneCols);
         return data && data.length != 0 ? headers + data : false;
      },
      "filename": function () {
         return "gene-data-" + getSelectedRowData(experimentList).label + ".csv";
      }
   });

   $("#factorExport").localDownload({
      "func": function () {
         var headers = headerCreate(factorCols);
         var data = dataToCsv(factorList.fnGetData(), factorCols);
         return data && data.length != 0 ? headers + data : false;
      },
      "filename": function () {
         return "factor-data-" + getSelectedRowData(geneList).geneabbrev + ".csv";
      }
   });

   fixAllTableWidths();

   var regInput = Brovine.newRegInput("#regFilter", function() {
      geneList.fnDraw();
   });

   $.fn.dataTableExt.afnFiltering.push(regInput.filter("geneList", 4));
});
