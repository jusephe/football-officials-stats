$.extend( $.fn.dataTable.defaults, {
    order: [ 0, 'asc' ],
    paging: false,
    searching: false,
    bInfo : false,
    language: {
        "sEmptyTable":     "Tabulka neobsahuje žádná data",
        "sInfo":           "Zobrazuji _START_ až _END_ z celkem _TOTAL_ záznamů",
        "sInfoEmpty":      "Zobrazuji 0 až 0 z 0 záznamů",
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
            "sNext":     "Další",
            "sPrevious": "Předchozí"
        },
        "oAria": {
            "sSortAscending":  ": aktivujte pro řazení sloupce vzestupně",
            "sSortDescending": ": aktivujte pro řazení sloupce sestupně"
        }
    }
} );

$(document).ready(function() {
    $('.stat-table-official').DataTable( {
        initComplete: function(settings, json) {
            this.api().columns('.sum').every(function() {
                var column = this;

                var sum = column
                    .data()
                    .reduce(function (a, b) {
                        a = parseInt(a, 10);
                        if(isNaN(a)){ a = 0; }

                        b = parseInt(b, 10);
                        if(isNaN(b)){ b = 0; }

                        return a + b;
                    });

                $(column.footer()).html(sum);
            });
        }
    } );
} );
