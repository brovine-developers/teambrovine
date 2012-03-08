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

