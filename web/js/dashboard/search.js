$(document).ready(function() {
    var body = $('body');

    body.on('click', '.simple-search', function() {
        doSimpleSearch();
    });
});

function doSimpleSearch() {
    var searchValue = $($('.data-value').filterByData('value-name', 'simpleSearchValue')).val();
    if(searchValue == null || searchValue == '') return;


}