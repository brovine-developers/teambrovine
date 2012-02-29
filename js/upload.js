$(document).ready(function() {
   var uploadTable = $('#uploadTable').dataTable({
      "sDom": "<'row'<'span4'l><'span8'f>r>t<'row'<'span4'i><'span4'p>>",
      "sPaginationType": "bootstrap",
      'bPaginate': false,
      "sAjaxSource": ("import/status?random=" + Math.random())
   });
   
   var uploadComplete = function(event, ID, fileObj, response, data) {
      var responseInfo = jQuery.parseJSON(response);

      // Iterate through the uploadTable data, look for the groupname.
      // Set the proper field as true.
      var tableData = uploadTable.fnGetData();
      var itemFound = false;
      var rowIndex = -1;

      if (responseInfo.success) {
         for (var i = 0; i < tableData.length; ++i) {
            if (tableData[i][0] == responseInfo.fileInfo.groupName) {
               itemFound = true;
               // Update the existing row.
               var allSuccess = true;
               for (var j = 1; j <= 3; ++j) {
                  if (responseInfo.whichFilesExist[j]) {
                     uploadTable.fnUpdate('Success', i, j);
                  } else {
                     allSuccess = false;
                  }
               }
               if (allSuccess) {
                  uploadTable.fnUpdate('Complete!', i, 4);
               } else {
                  uploadTable.fnUpdate(responseInfo.message, i, 4);
               }

               break;
            }
         }
      }

      if (!itemFound) {
         // Insert the new row.
         responseInfo.whichFilesExist.push(responseInfo.message);
         for (var i = 1; i <= 3; ++i) {
            if (responseInfo.whichFilesExist[i]) {
               responseInfo.whichFilesExist[i] = 'Success';
            } else {
               responseInfo.whichFilesExist[i] = 'Missing';
            }
         }
         uploadTable.fnAddData(responseInfo.whichFilesExist);
      }

      return true;
   }
   

   $('#file_upload').uploadify({
      'uploader': '/images/uploadify.swf',
      'script': '/import',
      'cancelImg': '/images/cancel.png',
      'expressInstall': '/images/expressInstall.swf',
      'auto': true,
      'removeCompleted': true,
      'multi': true,
      // 'fileExt': '* promoter TESS Hits 1.csv;* promoter TESS Job Parameters.csv;* promoter TESS Sequences.csv',
      'fileExt': '*.csv',
      'fileDesc': 'CSV TESS Results',
      onComplete: uploadComplete
   });

   $("#clearAllDataButton").click(function() {
      $.ajax("import/clearAllData?random=" + Math.random(), {
         success: function(data, textStatus) {
            $('#clearAllDataResult').text(data);
            uploadTable.fnClearTable();
         }
      });
   });
   

});
