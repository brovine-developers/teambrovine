/**
 * Updates a multi-select table.
 *
 * @param table the table to update
 * @param id name of column to use as a selector
 * @param loc name of ajax call
 * @param params parameters to send to server
 * @param clears function to be called every time table row is clicked
 * @param calls function to be called only when a click results in more than 0
 *              rows selected.
 */
function updateMultiselectList(table, id, loc, params, clears, calls) {
   clears();
   table.fnClearTable();

   jQuery.get("ajax/" + loc,
      params,
      function(data) {
         table.fnClearTable();
         table.fnAddData(data);

         table.$('tr').click(function(e) {
            if (!e.metaKey && !e.ctrlKey)
               table.$('tr').removeClass('selected');

            var specs = new Array();
            $(this).toggleClass('selected');

            table.$('tr.selected').each(function(i) {
               var rowData = table.fnGetData(this);
               specs[i] = rowData[id];
            });

            if (specs.length > 0) {
               calls(specs);
            }
         });
      },
      'json'
   );
}

/**
 * Updates a single-select table.
 *
 * @param table the table to update
 * @param id name of column to use as a selector
 * @param loc name of ajax call
 * @param params parameters to send to server
 * @param clears function to be called every time table row is clicked
 * @param calls function to be called only when a click results in more than 0
 *              rows selected.
 */
function updateSelectList(table, id, loc, params, before, clears, calls) {
   jQuery.get("ajax/" + loc,
      params,
      function(data) {
         before();
         table.fnClearTable();
         table.fnAddData(data);

         table.$('tr').click(function(e) {
            var specs = new Array();

            table.$('tr').removeClass('selected');
            $(this).addClass('selected');

            var rowData = table.fnGetData(this);
            specs[0] = rowData[id];

            clears();

            if (specs.length > 0) {
               calls(specs);
            }
         });
      },
      'json'
   );
}

function setupPlaceholders() {
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

   $('.dataTables_filter input').each(function() {
      $(this).width($(this).parent().width() - 10);
   });
}

function fixTableWidth(table) {
   table.css('width', '100%').fnAdjustColumnSizing();
   $('.dataTables_filter input').each(function() {
      $(this).width($(this).parent().width() - 10);
   });
}

