$(document).ready(function() {
    var body = $('body');

    body.on('click', '.simple-search', function() {
        doSimpleSearch();
    });

    body.on('click', '.complex-search', function(event) {
        event.preventDefault();
        doComplexSearch();
    });
});

function doComplexSearch() {
    $('.data-value').each(function(index, item) {
        var obj = $(item);
        var name = obj.data('value-name');

        var search = $($('.search-value').filterByData('entity', name));
        search.val(obj.val() == -1 ? '' : obj.val());
    });
    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args);
        $('#collapseSearch').collapse('hide');
    });
}

function doSimpleSearch() {
    var searchValue = $('.simple-search-value').val();
    if(searchValue == null || searchValue == '') return;

    $('.search-value').each(function(index, item) {
        $(item).val('');
    });

    var search = $($('.search-value').filterByData('entity', 'courses'));
    search.val(searchValue);

    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args);
    });
}

function setCount(args) {
    var found = args['totalFound'];
    $('.search-result-count').html(found);
}