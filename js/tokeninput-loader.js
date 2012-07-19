/**
 * Creates a tokenInput box out of a regular textbox. "elem" should be
 * a jquery selector pointing at one text box.
 */
function loadTokenInput(elem) {
   fireToken = function (item) { return updateRegFilter(elem); };

   $(elem).tokenInput("ajax/searchReg", onAdd: fireToken,
    onDelete: fireToken);
}

/**
 * Send an ajax call updating the Genes table each time regulation filter
 * item is added or deleted.
 */
function updateRegFilter(elem) {
   var tokens = $(elem).tokenInput("get");
   var names = [];

   $.each(tokens, function (idx, item) {
      names.push(item.name);
   });
}
