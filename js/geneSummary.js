var Brovine = Brovine || {};

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

            var geneId = rowData.geneabbrev;
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
         {"sTitle": "Regulation", "mDataProp": "regulation"},
         {"sTitle": "Chr", "mDataProp": "chromosome"},
         {"sTitle": "Start", "mDataProp": "start"},
         {"sTitle": "End", "mDataProp": "end"}
      ]

   });

   updateGeneSummary();
}

$(document).ready(function() {
   setupGeneSummary();
   setupPlaceholders();

   var regInput = Brovine.newRegInput("#regFilter", function() {
      expsPerGene.fnDraw();
   });

   $.fn.dataTableExt.afnFiltering.push(regInput.filter("experimentGene", 3));
});
