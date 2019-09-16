const $ = require('jquery');

// show/hide stats button
module.exports = function() {
    $('#show_hide').on('click', function () {
        if( $(this).data('show') ) { // show on last click
            $('.collapse:not(#menu)').collapse('hide');
            $(this).data('show', false); // set the data
        }
        else { // hide on last click
            $('.collapse:not(#menu)').collapse('show');
            $(this).data('show', true); // set the data
        }
    });
};
