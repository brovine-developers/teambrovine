
function updateSpeciesList() {
   jQuery.get("/ajax/getSpeciesList",
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
         updateComparisonList(curSpecies);
      });
   },
   'json'
   );
}

function updateSequenceList(geneid, transfac, study) {
   jQuery.get("/ajax/getSequenceList",
   {
      'geneid': geneid,
      'transfac': transfac,
      'study': study
   },
   function(data) {
      sequenceList.fnClearTable();
      sequenceList.fnAddData(data);
      fixTableWidth(sequenceList);
   },
   'json'
   );
}

function updateFactorList(geneid) {
   jQuery.get("/ajax/getFactorList",
   { 'geneid': geneid },
   function(data) {
      factorList.fnClearTable();
      sequenceList.fnClearTable();
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
   jQuery.get("/ajax/getGeneList",
   { 'experimentid': experimentid },
   function(data) {
      geneList.fnClearTable();
      sequenceList.fnClearTable();
      factorList.fnClearTable();

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

function fixTableWidth(table) {
   table.css('width', '100%').fnAdjustColumnSizing();
   $('.dataTables_filter input').each(function() {
      $(this).width($(this).parent().width() - 10);
   });
}

function updateExperimentList(comparisontypeid) {
   experimentList.fnClearTable();
   jQuery.get("/ajax/getExperimentList",
   {
      'comparisontypeid': comparisontypeid
   },
   function(data) {
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
   jQuery.get("/ajax/getComparisonList", 
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
   var secondRowHeight = "200px";
   var thirdRowHeight = "200px";
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

   $('.dataTables_filter label').each(function() {
      var text = $(this).text();
      $(this).find('input').attr('placeholder', text);

      // Taken from
      // http://stackoverflow.com/questions/5680201/jquery-remove-unwrapped-text-but-preserve-the-elements
      var parent = $(this)[0];  // Get reference to DOM

      for( var i = 0; i < parent.childNodes.length; i++ ) {
         var current_child = parent.childNodes[i];
         if( current_child.nodeType == 3 )
         parent.removeChild( current_child );
      }
   });
   
   $('#hierarchyPaneTab').on('shown', function (e) {
      fixTableWidth(speciesList);
      fixTableWidth(comparisonList);
      fixTableWidth(experimentList);
      fixTableWidth(geneList);
      fixTableWidth(factorList);
      fixTableWidth(sequenceList);
   });
   $('[placeholder]').focus(function() {
      var input = $(this);
      if (input.val() == input.attr('placeholder')) {
      input.val('');
      input.removeClass('placeholder');
      }
   }).blur(function() {
      var input = $(this);
      if (input.val() == '' || input.val() == input.attr('placeholder')) {
      input.addClass('placeholder');
      input.val(input.attr('placeholder'));
      }
   }).blur();

   // Get the list of species from the server.
   updateSpeciesList();
}

$(document).ready(function() {
   setupExperimentHierarchy();
      $('#testTable').dataTable({
         "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
         "sPaginationType": "bootstrap",
         "oLanguage": {
            "sLengthMenu": "_MENU_ records per page"
         }
      });

      var uploadTable = $('#uploadTable').dataTable({
         "sDom": "<'row'<'span4'l><'span8'f>r>t<'row'<'span4'i><'span4'p>>",
         "sPaginationType": "bootstrap",
         'bPaginate': false,
         "sAjaxSource": ("/import/status?random=" + Math.random())
      });

      var uploadComplete = function(event, ID, fileObj, response, data) {
         var responseInfo = jQuery.parseJSON(response);

         // Iterate through the uploadTable data, look for the groupname.
         // Set the proper field as true.
         var tableData = uploadTable.fnGetData();
         var itemFound = false;
         var rowIndex = -1;

         if (responseInfo.success) {
            for (var i = 0; i < tableData.length; ++i) {
               if (tableData[i][0] == responseInfo.fileInfo.groupName) {
                  itemFound = true;
                  // Update the existing row.
                  var allSuccess = true;
                  for (var j = 1; j <= 3; ++j) {
                     if (responseInfo.whichFilesExist[j]) {
                        uploadTable.fnUpdate('Success', i, j);
                     } else {
                        allSuccess = false;
                     }
                  }
                  if (allSuccess) {
                     uploadTable.fnUpdate('Complete!', i, 4);
                  } else {
                     uploadTable.fnUpdate(responseInfo.message, i, 4);
                  }

                  break;
               }
            }
         }

         if (!itemFound) {
            // Insert the new row.
            responseInfo.whichFilesExist.push(responseInfo.message);
            for (var i = 1; i <= 3; ++i) {
               if (responseInfo.whichFilesExist[i]) {
                  responseInfo.whichFilesExist[i] = 'Success';
               } else {
                  responseInfo.whichFilesExist[i] = 'Missing';
               }
            }
            uploadTable.fnAddData(responseInfo.whichFilesExist);
         }

         return true;
      }
      

      $('#file_upload').uploadify({
         'uploader': '/images/uploadify.swf',
         'script': '/import',
         'cancelImg': '/images/cancel.png',
         'expressInstall': '/images/expressInstall.swf',
         'auto': true,
         'removeCompleted': true,
         'multi': true,
         // 'fileExt': '* promoter TESS Hits 1.csv;* promoter TESS Job Parameters.csv;* promoter TESS Sequences.csv',
         'fileExt': '*.csv',
         'fileDesc': 'CSV TESS Results',
         onComplete: uploadComplete
      });

      $("#clearAllDataButton").click(function() {
         $.ajax("/import/clearAllData?random=" + Math.random(), {
            success: function(data, textStatus) {
               $('#clearAllDataResult').text(data);
               uploadTable.fnClearTable();
            }
         });
      });
});
