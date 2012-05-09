function updateTFSummary() {
   jQuery.get("ajax/getTFSummary",
      function(data) {
         tfSummary.fnClearTable();
         tfSummary.fnAddData(data);
         fixTableWidth(tfSummary);

         tfSummary.$('tr').click(function(e) {
            tfs = new Array();

            if (e.metaKey || e.ctrlKey) {
               $(this).toggleClass('selected');

               tfSummary.$('tr.selected').each(function(i) {
                  var rowData = tfSummary.fnGetData(this);
                  tfs.push(rowData.transfac);
               });
            }
            else {
               tfSummary.$('tr').removeClass('selected');
               $(this).addClass('selected');

               tfs.push(tfSummary.fnGetData(this).transfac);
            }

            if (tfs.length != 0) updateTFOccurrences(tfs);
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
         {"sTitle": "Genes", "mDataProp": "numGenes"},
         {"sTitle": "Occurrences", "mDataProp": "numOccs"}
      ]

   });

   tfOccur = $('#tf_occur').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Transfac Occurrences",
         "sInfo": "Showing _TOTAL_ occurrences",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": height,
      "aoColumns": [
         {"sTitle": "Comparison", "mDataProp": "celltype"},
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Gene Name", "mDataProp": "genename"},
         {"sTitle": "Model", "mDataProp": "study"},
         {"sTitle": "Species", "mDataProp": "species"},
         {"sTitle": "Beginning", "mDataProp": "beginning"},
         {"sTitle": "Sense", "mDataProp": "sense"},
         {"sTitle": "Length", "mDataProp": "length"}
      ]

   });

   updateTFSummary();
}

$(document).ready(function() {
   setupTFSummary();
   setupPlaceholders();
});

