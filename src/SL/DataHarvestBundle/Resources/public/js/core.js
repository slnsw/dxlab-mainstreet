// Define our global variables.
var cache = new Array();
var getArr = new Array();

// A placeholder callback function for jsonp response. Triggered automatically upon response when performing an ajax call marked with a response of type jsonp.
window.jsonpCallback = function(response, date, section){
    cache[section] = new Array();
    cache[section]['date'] = date;
    cache[section]['section'] = section;
    cache[section]['tags'] = response;
    getArr[section].resolve();
};

window.jsonpCallbacks = {};

(function($, w, d){
    var sections = new Array();
    var regex  = /([0-9]{4})/;
    // Initiate the setup upon document load.
    $(function(){
        // Get the current width of the window.
        var width = $(window).width(),
        dates = new Array(),
        wrapper = $('#tags-wrapper');
        images = $('img[class*="placeholder"]');
        // Loop through the items/images and push each date onto an array.
        $('div[id^="syditem"]').each(function(index, value){
            var date = $(value).attr('data-date');
            // Ensures the 'first' applicable year is stored in the correct format for later reference.
            date = date.match(regex);
            if(date
                && date.length > 0){
                dates.push(date[0]);
            }
        });
        // Sort the dates in ascending order.
        dates.sort();
        if(dates.length > 0){
            // Iterate through the dates in increments of 10 (decade), we only want a subset of the dates to provide sections for our page.
            j = 0;
            for(i = parseInt(dates[0]); i < parseInt(dates[dates.length - 1]); i+=10){
                sections[j] = i;
                j++;
            }
        }
        // If we have sections, retrieve the tags for each section. Initially this functionality was inside a loop with a closure, however this caused unexpected results with the number of results not reflecting the number of ajax calls.
        if(sections.length > 0){
            for(i = 0; i < sections.length; i++){
                (function(i){
                    var deferred = new $.Deferred();
                    // setTimeout(function(){
                    getTags(sections[i], i);
                    // }, i * 1500);
                    deferred.promise();
                    getArr.push(deferred);
                })(i);
            }
            // When all of the deferreds setup for each of the ajax calls have been resolved, create the template for the date and tags, and bind an onmousemove event handler to interchange the template content based on section. The window width and section width are also re-calculated based on window resize.
            $.when.apply($, getArr).then(function(){
                var sectionWidth = (width / sections.length);
                var current = 0;
                var template = _.template($('#tags').html());
                var funcs = {};
                func = _.bind(nearestPow, funcs);
                var opts = { nearestPow: func };
                // var data = _.extend({ name: sections[current], tags: cache[current]['tags'] }, opts);
                // wrapper.html(template(data));
                $(window).resize(function(event){
                    width = $(this).width();
                    sectionWidth = (width / sections.length);
                    docWidth = $(document).width();
                });
                images.on('mouseover', function(event){
                    $this = $(this);
                    var date = $this.parents('div').attr('data-date');
                    date = date.match(regex);
                    if(date
                        && date.length > 0){
                        var filtered = _.filter(cache, function(element){
                              return (element['date'] >= date[0]
                                        && element['date'] <= (parseInt(date[0]) + 10));
                            });
                        if(filtered.length > 0){
                            section = filtered[0]['section'];
                            // This check saves constant recalls to update the template if in the same section.
                            if(section != current){
                                // Update the section to the current.
                                current = section;
                                if(typeof cache[current] !== 'undefined'){
                                    var wrappingDiv = $('<div></div>', {
                                        id: 'tags-wrapper-inner'
                                    });
                                    var data = _.extend({ name: sections[current], tags: cache[current]['tags'] }, opts);
                                    wrapper.html(wrappingDiv.html(template(data)));
                                }
                            }
                        }
                    }
                });
            });
        }
    });

    // A function assigned to a variable for performing the necessary ajax call to retrieve tags based on date.
    var getTags = function(date, section)
    {
        window.jsonpCallbacks['callback' + section] = function(response){
            window.jsonpCallback(response, date, section);
        };
        var jqxr = $.ajax(
            'app.php/filter_tags/' + date,
            {
                dataType: 'jsonp',
                // The dynamic jsonp callback wrapping the response. Unusually by definining ajax call specific jsonp callbacks, the relative callback is always called. This is only necessary performing an ajax call within a loop to simulate multiple requests.
                jsonpCallback: 'window.jsonpCallbacks.callback' + section,
                async: true,
            }
        ).done(function(response){
            // This doesn't appear to be called due to jQuery complaining that the jsonp callback wasn't called, even though it is?...
        }).always(function(response, status, jqxr){
        });
    }

    var nearestPow = function(number, pow){
        return Math.pow(pow, Math.round(Math.log(number) / Math.log( pow )));
    }

})(jQuery, window, document);