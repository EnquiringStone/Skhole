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
    var collapse = $('#collapseSearch');
    $('.data-value').each(function(index, item) {
        var obj = $(item);
        var name = obj.data('value-name');

        var search = $($('.search-value').filterByData('entity', name));
        search.val(obj.val() == -1 ? '' : obj.val());
    });
    doSimpleSearch();
    collapse.trigger('collapse');
}

function doSimpleSearch() {
    var searchValue = $('.simple-search-value').val();
    if(searchValue == null || searchValue == '') return;

    var search = $($('.search-value').filterByData('entity', 'courses'));
    search.val(searchValue);

    refreshPage($('.search-enabled'), true, true);
}