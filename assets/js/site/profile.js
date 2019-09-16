const $ = require('jquery');

require( 'datatables.net-bs4' )(window, $);

// --------------------------- DATATABLES ---------------------------

$.extend( $.fn.dataTable.defaults, {
    order: [ 0, 'asc' ],
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
    $('.stat-table-official-simple').DataTable( {
        paging: false,
        searching: false,
        bInfo : false,
        initComplete: function(settings, json) {
            this.api().columns('.sum').every(function() {
                var column = this;

                if (column.data().any()) {
                    var sum = column
                        .data()
                        .reduce(function(a, b) {
                            a = parseInt(a, 10);
                            if(isNaN(a)){ a = 0; }

                            b = parseInt(b, 10);
                            if(isNaN(b)){ b = 0; }

                            return a + b;
                        });

                    $(column.footer()).html(sum);
                }
            });
        }
    } );

    $('.stat-table-official-inter').DataTable( {
        order: [ 5, 'desc' ],
    } );
} );

// ------------------------ END OF DATATABLES ------------------------

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
