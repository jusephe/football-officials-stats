const $ = require('jquery');

// show and hide stats buttons
module.exports = function() {
    $('#show_all').on('click', function () {
        $('.collapse:not(#menu)').collapse('show');
    });
    $('#hide_all').on('click', function () {
        $('.collapse:not(#menu)').collapse('hide');
    });
};
