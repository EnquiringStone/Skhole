$(document).ready(function() {
    var body = $('body');
    var searchResults = $('#search-results');

    collapsePanel(searchResults);

    $('#simple-search').on('submit', function (event) {
        event.preventDefault();
        doSimpleSearch();
    });

    $('#complex-search').on('submit', function (event) {
        event.preventDefault();
        doComplexSearch();
    });

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.add-to-collection', function() {
        var context = $(this);
        var courseId = context.data('course-id');
        var url = context.data('url');

        var hrefUrl = context.data('href-url');
        var text = context.data('name');

        var args = {};
        args['method'] = 'addToCollection';
        args['ajax_key'] = 'CCOAS1';
        args['courseId'] = courseId;

        sendAjaxCall(url, args, function() {
            context.after('<a href="'+hrefUrl+'" class="btn btn-primary btn-sm">'+text+'</a>');
            context.remove();
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    });
});

function collapsePanel(context) {
    var panel = $(context.parents('.panel'));
    var span = $('.chevron', panel);
    if ($('.glyphicon', span).hasClass('glyphicon-chevron-up'))
        collapseByChevronSpan(span);
}

function showPanel(context) {
    var panel = $(context.parents('.panel'));
    var span = $('.chevron', panel);
    if ($('.glyphicon', span).hasClass('glyphicon-chevron-down'))
        collapseByChevronSpan(span);
}

function doComplexSearch() {
    $('.data-value').each(function(index, item) {
        var obj = $(item);
        var name = obj.data('value-name');

        var search = $($('.search-value').filterByData('entity', name));
        search.val(obj.val() == -1 ? '' : obj.val());
    });
    addLoadingScreen($($('#search-forms').parents('.panel')));
    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args);
        removeLoadingScreen();
    }, true);
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
    addLoadingScreen($($('#search-forms').parents('.panel')));
    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args);
        removeLoadingScreen();
    }, true);
    updateReviewModals();
}

function setCount(args) {
    var found = args['totalFound'];
    $('.search-result-count').html(found);
    if (found <= 0) collapsePanel($('#search-results'));
    else showPanel($('#search-results'));
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