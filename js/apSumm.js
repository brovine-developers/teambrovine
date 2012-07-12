function updateAPSummary(tf) {
   jQuery.get("ajax/getApriori",
      function(data) {
         apSumm.fnClearTable();
         apSumm.fnAddData(data);
         fixTableWidth(apSumm);
      },
      'json'
   );
}

function setupAPSummary() {
   var height = "200px";

   apSumm = $('#tf_freq').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Frequently Occurring Factors",
         "sInfo": "Showing _TOTAL_ associations",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": height,
      "aoColumns": [
         {"sTitle": "Transfacs", "mDataProp": "items"},
         {"sTitle": "Count", "mDataProp": "count"},
         {"sTitle": "Support", "mDataProp": "sup"},
         {"sTitle": "Num Items", "mDataProp": "numItems", "bVisible": false}
      ],
      "aaSortingFixed": [[3, "desc"]]
   });

   updateAPSummary();
}

$(document).ready(function() {
   setupAPSummary();
   setupPlaceholders();
});

