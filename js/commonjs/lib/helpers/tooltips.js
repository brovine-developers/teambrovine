// Tooltips
module.exports = (function () {
   var tooltips = {
      'th': {
         'Chr': 'Chromosome',
         'Reg': 'Regulation',
         'Gene': 'Hover for full name',
         'Factors': 'Unique factor / study pairs',
         'Genes': 'Total number of genes',
         '#': 'Number of matching regulatory elements'
      }
   };

   var init = function () {
      // th (table header) based tooltips
      $.each(tooltips.th, function (key, value) {
         $('th:contains(' + key + ')').tooltip({
            'title': value
         });
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

      // Setup tooltips on gene rows in the sequence table.
      $('#sequenceList').tooltip({
         selector: 'td:nth-child(2)',
         title: function() {
            if ($(this).hasClass('dataTables_empty')) {
               return "Select a factor first.";
            }
            var row = ($(this).parent()[0]);
            var rowData = sequenceList.fnGetData(row);
            return rowData.genename;
         }
      });
   };

   return {
      "init": init
   };
}) ();
