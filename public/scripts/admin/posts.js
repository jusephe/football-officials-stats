let del_buttons = document.getElementsByClassName('btn-danger');

for( let i = 0; i < del_buttons.length; i++ ) {
    del_buttons[i].addEventListener( 'click', function(event){
        if ( window.confirm('Opravdu chcete novinku smazat?') ) {
            return true;
        }
        else {
            event.preventDefault();
            return false
        }
    } );
}
