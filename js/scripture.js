(function ($) {
    
    /**
     * Create a link with embedded data which the user can download.
     * Useful when there's already local data that the user may want
     * to save, such as a table.
     * 
     * Options:
     *  - opts.filename: name that should be used when saving the file
     *  - opts.data: data that will be saved to the user's computer if the link
     *     is clicked. If this is null or empty (""), this function does
     *     nothing. Should be a string. Data will be URI encoded before being
     *     embedded in the link.
     *  - opts.func: function to be called on click to automatically update the
     *     data embedded in the link. If this is undefined or null, no click
     *     handler will be set. Must be a function. If the return value from
     *     this function is blank, false, etc., 
     */
    $.fn.localDownload = function (opts) {
        // Set up default settings for filename
        var settings = $.extend({
            "filename": "temp_file",
            "local": true, // assume the browser does support local download.
            "serverType": "form", // used when local=false. one of "GET" or "form".
            "bounceUrl": "/ajax/saveFile"
        }, opts);

        var setHref = function (settings, data, scope) {
            var header = (settings.local) ? "data:octet-stream," : settings.bounceUrl;

            if (!data) {
                $(scope).attr("href", "#");
            }
            else {
                var requestLink;

                if (settings.local)
                    requestLink = header + encodeURI(data);
                /*else if (settings.serverType === "POST")
                    requestLink = header;
                */
                else if (settings.serverType === "form")
                    requestLink = "#";

                $(scope).attr("href", requestLink);
            }
        };

        var createForm = function (data, url, filename) {
            return $('<form></form>').hide()
              .attr({ target: '_blank', method: 'post', action: url })
              .append($('<input />')
                .attr({ type: 'hidden', name: 'data', value: data })
              )
              .append($('<input />')
                .attr({ type: 'hidden', name: 'filename', value: filename })
              );
        };
        
        // If there's data, turn every element into a download link
        return this.each(function () {
            setHref(settings, settings.data, this);
            
            if (!(settings.filename instanceof Function))
                $(this).attr("download", settings.filename);
            
            if (settings.func) {
                $(this).click(function (event) {
                    var data = settings.func(); 

                    if (data) {
                        if (settings.filename instanceof Function)
                            $(this).attr("download", settings.filename());

                        setHref(settings, data, this);

                        if (!settings.local) {
                            /*if (settings.serverType === "POST") {
                                $.ajax({
                                    url: settings.bounceUrl,
                                    type: "POST",
                                    data: data,
                                    success: function (xhr, status, err) {

                                    },
                                    error: function (data, status, xhr) {

                                    }
                                });

                                return false;
                            }
                            else*/ if (settings.serverType === "form") {
                                createForm(data, settings.bounceUrl
                                  , settings.filename).submit();
                                return false;
                            }
                        }
                    }
                    else {
                        alert("No data to download");
                        return false;
                    }
                });
            }
        }); // but do nothing if there's no data
    };
    
} ($));