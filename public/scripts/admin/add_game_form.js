// Setup an "add a yellow card" link
var $addYellowButton = $('<button type="button" class="btn btn-outline-primary btn-sm">+ Přidat žlutou kartu</button>');
var $div_col10 = $('<div class="col-sm-10"></div>').append($addYellowButton);
var $div_col2 = $('<div class="col-sm-2"></div>');
var $newYellowLinkDiv = $('<div class="form-group row"></div>').append($div_col2);
$newYellowLinkDiv = $newYellowLinkDiv.append($div_col10);

// Setup an "add a red card" link
var $addRedButton = $('<button type="button" class="btn btn-outline-primary btn-sm">+ Přidat červenou kartu</button>');
var $div_col10_red = $('<div class="col-sm-10"></div>').append($addRedButton);
var $div_col2_red = $('<div class="col-sm-2"></div>');
var $newRedLinkDiv = $('<div class="form-group row"></div>').append($div_col2_red);
$newRedLinkDiv = $newRedLinkDiv.append($div_col10_red);

jQuery(document).ready(function() {
    addDeleteInit('yellowCards', $newYellowLinkDiv, $addYellowButton);
    addDeleteInit('redCards', $newRedLinkDiv, $addRedButton);
});

function addDeleteInit($typeOfCard, $newCardLinkDiv, $addCardButton) {
    // Get the div that holds the collection of cards
    var $collectionHolder = $('div#game_' + $typeOfCard);

    // Add a delete link to all of the existing card form div elements
    var $card;
    $collectionHolder.children('div.form-group.row').each(function() {
        $card = ($(this).find('div[id^="game_' + $typeOfCard + '_"]')).first();  // there is only one
        addCardFormDeleteLink($card, $(this));
    });

    // Add the "add a card" div with button to the div with cards
    $collectionHolder.append($newCardLinkDiv);

    // Count the current form inputs we have (e.g. 2), use that as the new index when inserting a new item
    $collectionHolder.data('index', $collectionHolder.find('input[type="number"]').length);

    $addCardButton.on('click', function(e) {
        // Add a new card form (see next code block)
        addCardForm($collectionHolder, $newCardLinkDiv, $typeOfCard);
    });
}

function addCardForm($collectionHolder, $newCardLinkDiv, $typeOfCard) {
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
    $newForm.find('div[id^="game_' + $typeOfCard + '_"]').each(function() {
        addCardFormDeleteLink($(this), $newForm);
    });
}

// first param "where to append", second param "what to remove"
function addCardFormDeleteLink($FormDiv, $Form) {
    var $removeFormButton = $('<button type="button" class="btn btn-outline-danger btn-sm">Odebrat tuto kartu</button>');
    $FormDiv.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // Remove the div with card form
        $Form.remove();
    });
}
