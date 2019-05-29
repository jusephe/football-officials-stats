// Setup an "add a red card" link
var $addRedButton = $('<button type="button" class="btn btn-outline-primary btn-sm">+ Přidat</button>');
var $div_col10_red = $('<div class="col-sm-10"></div>').append($addRedButton);
var $div_col2_red = $('<div class="col-sm-2"></div>');
var $newRedLinkDiv = $('<div class="form-group row"></div>').append($div_col2_red);
$newRedLinkDiv = $newRedLinkDiv.append($div_col10_red);

jQuery(document).ready(function() {
    addDeleteInit($newRedLinkDiv, $addRedButton);
});

function addDeleteInit($newCardLinkDiv, $addCardButton) {
    // Get the div that holds the collection of cards
    var $collectionHolder = $('div#game_punishments_redCards');

    // Add the "add a card" div with button to the div with cards
    $collectionHolder.append($newCardLinkDiv);

    // Count the current form inputs we have (e.g. 2), use that as the new index when inserting a new item
    $collectionHolder.data('index', $collectionHolder.find('input[type="text"]').length);

    $addCardButton.on('click', function(e) {
        // Add a new card form (see next code block)
        addCardForm($collectionHolder, $newCardLinkDiv);
    });
}

function addCardForm($collectionHolder, $newCardLinkDiv) {
    // Get the data-prototype
    var prototype = $collectionHolder.data('prototype');

    // Get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;

    // Replace '__name__' in the prototype's HTML to a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // Increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Convert to jQuery object
    var $newForm = $(newForm);

    // Display the form in the page (it is a div), before the "add a card" link div
    $newCardLinkDiv.before($newForm);

    // Add a delete link to the new form
    $newForm.find('div[id^="game_punishments_redCards_"]').each(function() {
        addCardFormDeleteLink($(this), $newForm);
    });
}

// first param "where to append", second param "what to remove"
function addCardFormDeleteLink($FormDiv, $Form) {
    var $removeFormButton = $('<button type="button" class="btn btn-outline-danger btn-sm">Odebrat</button>');
    $FormDiv.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // Remove the div with card form
        $Form.remove();
    });
}
