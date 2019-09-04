// add path to updateStats controller to button + display loading after click
$('#update_stats').on('click', function () {
    location.href = $('#update_stats').data('path');

    $('.loading').removeClass('d-none');

    $('h1').hide();
});
