$(document).ready(function() {
    var body = $('body');
    var searchResults = $('#search-results');

    $('#category-picker').hide();

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

    body.on('click', '.search-for-category', function () {
        reset();

        var element = $(this);
        var categoryId = element.data('category-id');

        var searchValue = $('.search-value').filterByData('entity', 'categories');
        $(searchValue).val(categoryId);

        var formControl = $('.form-control').filterByData('value-name', 'categories');
        $(formControl).val(categoryId);

        doComplexSearch();
    });

    $('#show-search-categories').on('click', function () {
        var element = $('#category-picker');
        if (element.is(':visible'))
            element.hide();
        else
            element.show();
    });

    $('#show-most-popular-courses').on('click', function () {
        addLoadingScreen($($('#search-forms').parents('.panel')));
        var url = $(this).data('url');
        sendAjaxCall(url, {'ajax_key': 'SAS2', 'method': 'getMostViewedCourses'}, function (data) {
            reset();
            $('.sort-field', searchResults).empty();
            $('.pagination-field', searchResults).empty();
            $('.content-field', searchResults).html(data['html']);
            $('.review-modals-div', $(searchResults.parent())).html(data['modalHtml']);
            setCount(data, $('.search-enabled'));
            removeLoadingScreen();
            moveToResultPanel();
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        })
    });

    $(document).on('refreshPageSucceeded', function () {
        updateReviewModals();
    });
});

function reset() {
    $('.search-value').each(function(index, item) {
        $(item).attr('value', '');
    });

    $('.simple-search-value').val('');

    $('.data-value', '#complex-search').each(function () {
        var item = $(this);
        if (item.is('input'))
            item.val('');
        else
            item.val('-1');
    });
}

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
    var search = $($('.search-value').filterByData('entity', 'courses'));
    search.val($('.simple-search-value').val());

    $('.data-value').each(function(index, item) {
        var obj = $(item);
        var name = obj.data('value-name');

        var search = $($('.search-value').filterByData('entity', name));
        search.val(obj.val() == -1 ? '' : obj.val());
    });
    addLoadingScreen($($('#search-forms').parents('.panel')));
    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args, $('.search-enabled'));
        removeLoadingScreen();
        moveToResultPanel();
    }, true);
}

function doSimpleSearch() {
    var searchValue = $('.simple-search-value').val();
    if(searchValue == null || searchValue == '') return;

    reset();

    $('.simple-search-value').val(searchValue);

    var search = $($('.search-value').filterByData('entity', 'courses'));
    search.val(searchValue);
    addLoadingScreen($($('#search-forms').parents('.panel')));
    refreshPage($('.search-enabled'), true, true, function(args) {
        setCount(args, $('.search-enabled'));
        removeLoadingScreen();
        moveToResultPanel();
    }, true);
}

function setCount(args, caller) {
    var found = args['totalFound'];
    $('.search-result-count').html(found);
    showPanel($('#search-results'));

    if (found <= 0)
    {
        var base = $($(caller).parents('.page-controls'));
        $('.content-field', base).append('<p class="lead centrum">'+$('#none-found').val()+'</p>');
        $('.sort-field', base).empty();
    }
}

function moveToResultPanel() {
    $("body, html").animate({
        scrollTop: $( $('#search-results') ).offset().top - 35
    }, 600);
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