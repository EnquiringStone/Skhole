$(document).ready(function() {
    var body = $('body');

    body.on('click', '.simple-search', function() {
        doSimpleSearch();
    });

    body.on('click', '.complex-search', function(event) {
        event.preventDefault();
        doComplexSearch();
    });

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
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
    updateReviewModals();
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
    updateReviewModals();
}

function setCount(args) {
    var found = args['totalFound'];
    $('.search-result-count').html(found);
}

function updateReviewModals() {
    var data = $('#search-results');
    var args = {};

    var search = getSearch(data);
    var pagination = getPagination(data);
    var sort = getSort(data);

    args['method'] = 'getReviewModals';
    args['context'] = 'SEARCH';
    args['ajax_key'] = 'CSAS1';

    args['search'] = search;
    args['correlation'] = data.data('correlation');
    if(sort != null) {
        args['sortAttribute'] = sort['sortAttribute'];
        args['sortValue'] = sort['sortValue'];
    }
    if(pagination != null) {
        args['offset'] = pagination['offset'];
        args['limit'] = pagination['limit'];
    }
    args['entity'] = data.data('entity');

    sendAjaxCall(data.data('url'), args, function(data) {
        var reviewModalsDiv = $('.review-modals-div');
        reviewModalsDiv.empty();
        reviewModalsDiv.append(data['html']);
    }, function() {

    });
}