function updateAPSummary(tf) {
   jQuery.get("ajax/getFrequentItemsets",
      { 'min_sup': 0.85, 'max_sup': 0.95 },
      function(data) {
         var total = data.itemCnt;
         var tableData = [];
         apSumm.fnClearTable();

         for (var name in data.data) {
            var cnt = data.data[name];
            tableData.push({
               "items": name,
               "count": cnt,
               "sup": (Math.round((cnt / total) * 10000) / 100).toString() + "%",
               "numItems": name.split(",").length
            });
         }

         apSumm.fnAddData(tableData);
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

