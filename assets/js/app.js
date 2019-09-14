/*
 * Welcome to your app's main JavaScript file!
 */

// any CSS you require will output into a single css file (app.scss in this case)
require('../css/app.scss');

const $ = require('jquery');

require('bootstrap');

$(document).ready(function() {
    $('li.active').removeClass('active');
    $('a[href="' + location.pathname + '"]').closest('li').addClass('active');
});
