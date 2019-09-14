const $ = require('jquery');

// hide charts after their drawing finished + hide loading after charts are hidden
$(document).ready(function() {
    $('#red_offence_chart').collapse('hide');
    $('#cards_minutes_chart').collapse('hide');

    $("#red_offence_chart").on("hidden.bs.collapse", function(){
        $("#cards_minutes_chart").on("hidden.bs.collapse", function(){
            $('.loading').hide();
        });
    });
} );
