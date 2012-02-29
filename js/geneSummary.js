function updateGeneSummary() {
   jQuery.get("ajax/getGeneSummary",
      function(data) {
         geneSummary.fnClearTable();
         geneSummary.fnAddData(data);
         fixTableWidth(geneSummary);

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

function setupGeneSummary() {
   var height = "600px";   

   geneSummary = $('#geneList_summ').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Genes",
         "sInfo": "Showing _TOTAL_ genes",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": height,
      "aoColumns": [
         {"sTitle": "Gene Name", "mDataProp": "genename"},
         {"sTitle": "Gene Abbrev", "mDataProp": "geneabbrev"},
         {"sTitle": "Chr", "mDataProp": "chromosome"},
         {"sTitle": "Start", "mDataProp": "start"},
         {"sTitle": "End", "mDataProp": "end"},
         {"sTitle": "Num Comps", "mDataProp": "numComps"},
         {"sTitle": "Num Exps", "mDataProp": "numExps"},
      ]

   });

   updateGeneSummary();
}

$(document).ready(function() {
   setupGeneSummary();
   setupPlaceholders();
});
