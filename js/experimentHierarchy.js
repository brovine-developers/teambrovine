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

function setupEditAndDelete() {
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
      var newGeneData = {
         geneid: $('#geneidInput').val(),
         genename: $('#genenameInput').val(),
         geneabbrev: $('#geneabbrevInput').val(),
         chromosome: $('#genechromosomeInput').val(),
         start: $('#genestartInput').val(),
         end: $('#geneendInput').val(),
         regulation: geneRegulation,
         date_edited: getTimestamp(),
         date_edited_pretty: getPrettyTime(),
         numFactors: oldGeneData.numFactors
      };

      geneList.fnUpdate(newGeneData, row);
      geneModal.modal('hide');

      jQuery.post('ajax/updateGene', newGeneData, function() {
         // Do nothing on success, I guess.
         // Maybe we should put up a spinner but who's got the time.
         // This looks faster, too.
      });
   });
}

function updateSpeciesList() {
   jQuery.get("ajax/getSpeciesList",
   function(data) {
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editFactor').addClass('disabled');
      $('#hideFactor').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

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
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

      sequenceList.fnClearTable();
      $('#sequenceInfo').empty();
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
      $('#sequenceInfo').empty();
      $('#editFactor').addClass('disabled');
      $('#hideFactor').addClass('disabled');
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
         if (!rowData.allRow) {
            $('#editFactor').removeClass('disabled');
            $('#hideFactor').removeClass('disabled');
         } else {
            $('#editFactor').addClass('disabled');
            $('#hideFactor').addClass('disabled');
         }
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
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editFactor').addClass('disabled');
      $('#hideFactor').addClass('disabled');
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

function updateExperimentList(comparisontypeid) {
   jQuery.get("ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid
   },
   function(data) {
      $('#editGene').addClass('disabled');
      $('#hideGene').addClass('disabled');
      $('#editFactor').addClass('disabled');
      $('#hideFactor').addClass('disabled');
      $('#editSequence').addClass('disabled');
      $('#hideSequence').addClass('disabled');

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
         $('#editGene').addClass('disabled');
         $('#hideGene').addClass('disabled');
         $('#editFactor').addClass('disabled');
         $('#hideFactor').addClass('disabled');
         $('#editSequence').addClass('disabled');
         $('#hideSequence').addClass('disabled');

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
         {"sTitle": "Sc", "mDataProp": "sc", "sType": "numeric"},
         {"sTitle": "Sm", "mDataProp": "sm", "sType": "numeric"},
         {"sTitle": "Spv", "mDataProp": "spv", "sType": "numeric"},
         {"sTitle": "Ppv", "mDataProp": "ppv", "sType": "numeric"},
         {"sTitle": "Sequence", "mDataProp": "sequence"},
         {"sTitle": "Sequenceid", "mDataProp": "seqid", "bVisible": false}
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
