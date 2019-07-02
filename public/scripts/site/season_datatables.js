$.extend( $.fn.dataTable.defaults, {
    order: [ 1, 'desc' ],
    language: {
        "sEmptyTable":     "Tabulka neobsahuje žádná data",
        "sInfo":           "_START_ až _END_ z celkem _TOTAL_ záznamů",
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
