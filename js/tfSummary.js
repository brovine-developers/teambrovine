function updateTFSummary() {
   jQuery.get("ajax/getTFSummary",
      function(data) {
         tfSummary.fnClearTable();
         tfSummary.fnAddData(data);
         fixTableWidth(tfSummary);

         /*comparisonList.$('tr').click(function(e) {
            comparisonList.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = comparisonList.fnGetData(this);

            var comparisonTypeId = rowData.comparisontypeid;
            geneList.fnClearTable();
            factorList.fnClearTable();
            sequenceList.fnClearTable();
            updateExperimentList(comparisonTypeId);
         });*/
      },
      'json'
   );
}

function setupTFSummary() {
   var height = "200px";

   tfSummary = $('#tfList_summ').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Transcription Factors",
         "sInfo": "Showing _TOTAL_ transcription factors",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": height,
      "aoColumns": [
         {"sTitle": "Transfac", "mDataProp": "transfac"},
         {"sTitle": "Studies", "mDataProp": "numStudies"},
         {"sTitle": "Genes", "mDataProp": "numGenes"},
         {"sTitle": "Occurrences", "mDataProp": "numOccs"},
      ]

   });

   updateTFSummary();
}

$(document).ready(function() {
   setupTFSummary();
   setupPlaceholders();
});

