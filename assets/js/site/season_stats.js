const $ = require('jquery');

require( 'datatables.net-bs4' )(window, $);

// --------------------------- DATATABLES ---------------------------

$.extend( $.fn.dataTable.defaults, {
    order: [ 1, 'desc' ],
    language: {
        "sEmptyTable":     "Tabulka neobsahuje žádná data",
        "sInfo":           "_START_ až _END_ z _TOTAL_ záznamů",
        "sInfoEmpty":      "",
        "sInfoFiltered":   "(filtrováno z celkem _MAX_ záznamů)",
        "sInfoPostFix":    "",
        "sInfoThousands":  " ",
        "sLengthMenu":     "Zobraz _MENU_ záznamů",
        "sLoadingRecords": "Načítám...",
        "sProcessing":     "Provádím...",
        "sSearch":         "Hledat:",
        "sZeroRecords":    "Žádné záznamy nebyly nalezeny",
        "oPaginate": {
            "sFirst":    "První",
            "sLast":     "Poslední",
            "sNext":     ">",
            "sPrevious": "<"
        },
        "oAria": {
            "sSortAscending":  ": aktivujte pro řazení sloupce vzestupně",
            "sSortDescending": ": aktivujte pro řazení sloupce sestupně"
        }
    }
} );

$(document).ready(function() {
    $('.stat-table').DataTable( {
    } );

    $('.stat-table-3cols').DataTable( {
        order: [ 2, 'desc' ]
    } );
} );

// ------------------------ END OF DATATABLES ------------------------

// hide chart after its drawing finished + hide loading after chart is hidden
$(document).ready(function() {
    $('#red_offence_chart').collapse('hide');

    $("#red_offence_chart").on("hidden.bs.collapse", function(){
        $('.loading').hide();
    });
} );

// show/hide stats button
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
