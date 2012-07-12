function updateGeneSummary() {
   jQuery.get("ajax/getGeneSummary",
      function(data) {
         geneSummary.fnClearTable();
         geneSummary.fnAddData(data);
         fixTableWidth(geneSummary);

         geneSummary.$('tr').click(function(e) {
            geneSummary.$('tr').removeClass('selected');
            $(this).addClass('selected');
            var rowData = geneSummary.fnGetData(this);

            var geneId = rowData.genename;
            updateExpsPerGene(geneId);
         });
      },
      'json'
   ); 
}

function updateExpsPerGene(geneId) {
   jQuery.get("ajax/getExpsPerGene",
                                        {
"geneid": geneId
                                        },
      function(data) {
         expsPerGene.fnClearTable();  
         expsPerGene.fnAddData(data);
         fixTableWidth(expsPerGene);
      },
      'json'
   );

   jQuery.get("ajax/getLongGene",
    {
       "geneid": geneId
    },
    function (data) {
       $("#gene-display").css("display", "block");
       $("#gene-abbrev").html(data[0].geneabbrev);
       $("#gene-name").html(data[0].genename);
       $("#gene-chrom").html(data[0].chromosome);
       $("#gene-start").html(data[0].start);
       $("#gene-end").html(data[0].end);
       $("#gene-length").html(data[0].length);
    },
    'json'
   );
}

function setupGeneSummary() {
   var height = "200px";   

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
         {"sTitle": "Gene", "mDataProp": "genename"},
         {"sTitle": "Abbrev", "mDataProp": "geneabbrev"},
         {"sTitle": "Chr", "mDataProp": "chromosome"},
         {"sTitle": "Start", "mDataProp": "start"},
         {"sTitle": "End", "mDataProp": "end"},
         {"sTitle": "Comps", "mDataProp": "numComps"},
         {"sTitle": "Exps", "mDataProp": "numExps"}
      ]

   });

   expsPerGene = $('#experimentGene').dataTable({
      "sDom": "<'row'<'span12'f>r>t<'row'<'span12'i>>",
      "bPaginate": false,
      "oLanguage": {
         "sSearch": "Search Related Experiments",
         "sInfo": "Showing _TOTAL_ experiments",
         "sInfoFiltered": " of _MAX_"
      },
      "sScrollY": height,
      "aoColumns": [ 
         {"sTitle": "Experiment", "mDataProp": "label"},
         {"sTitle": "Comparison", "mDataProp": "celltype"},
         {"sTitle": "Species", "mDataProp": "species"},
         {"sTitle": "Regulation", "mDataProp": "regulation"}
      ]

   });

   updateGeneSummary();
}

$(document).ready(function() {
   setupGeneSummary();
   setupPlaceholders();
});
