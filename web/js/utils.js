/**
 * Created by johan on 02-Dec-15.
 */
function showAjaxErrorModal(message)
{
    var modal = $('');
    modal.show();
    //make error modal unhidden
    //add click listener to see if person clicks outside the modal
    $(modal, '.modal-button').click(hideModal(modal));
    $(modal, '.modal-message').html(message);
    //set content (right tag) to message
}

//Hides the given modal. The modal is the entire modal div
function hideModal(modal) {
    $(modal).hide();
    $(modal, '.modal-message').html('');
    //empty content
}

function onSortChange(caller, successCallBack, errorCallBack) {
    var sort = $(caller);
    var url = sort.data('url');
    var entity = sort.data('entity');
    var attribute = $(sort.find(':selected')).data('attribute');
    var order = $(sort.find(':selected')).data('sorted');
    var method = sort.data('method');
    var key = sort.data('key');

    sendAjaxCall(url, {'entity' : entity, 'attribute' : attribute, 'order' : order, 'offset': getCurrentOffset($(caller)),
        'ajax_key' : key, 'method' : method}, successCallBack, errorCallBack);
}

function onPaginationClick(caller, successCallBack, errorCallBack) {
    var pagination = $($(caller).parents('.pagination'));
    var li = $($(caller).parent());
    if(li.hasClass('active'))
        return;
    $('.active', pagination).removeClass('active');
    li.addClass('active');

    var url = pagination.data('url');
    var entity = pagination.data('entity');
    var offset = li.data('offset');
    var method = pagination.data('method');
    var key = pagination.data('key');

    var data = {'entity': entity, 'offset': offset, 'method': method, 'ajax_key': key};
    var sort = getCurrentOrder($(caller));

    if(sort != null) {
        data['attribute'] = sort.attribute;
        data['order'] = sort.order;
    }

    sendAjaxCall(url, data, successCallBack, errorCallBack);
}

function sendAjaxCall(url, contents, successCallBack, errorCallBack) {
    $.ajax({
        method: "POST",
        url: url,
        data: contents,
        dataType: "json",
        success: function(data) {
            successCallBack(data);
        },
        error: function(message) {
            errorCallBack(message);
        }
    });
}

function refreshPage(caller, resetPagination, resetSort) {
    var base = $($(caller).parents('.page-controls'));

    var entity = base.data('entity');
    var url = base.data('url');
    var context = base.data('context');
    var view = base.data('view');

    var arguments = {};

    var pagination = getPagination(base);
    var sort = getSort(base);
    var search = getSearch(base);
    var defaultSearchValues = getSearchValues(base);

    if(pagination != null) {
        if(!resetPagination) {
            arguments['offset'] = pagination['offset'];
            arguments['limit'] = pagination['limit'];
        }
        arguments['pageName'] = pagination['pageName'];
    } else {
        arguments['pageName'] = 'empty';
    }
    if(sort != null && !resetSort) {
        arguments['sortAttribute'] = sort['sortAttribute'];
        arguments['sortValue'] = sort['sortValue'];
    }
    if(search != null) {
        arguments['searchAttributes'] = search;
    }
    if(defaultSearchValues != null) {
        arguments['searchValues'] = defaultSearchValues;
    }

    arguments['entity'] = entity;
    arguments['ajax_key'] = 'PCAS1';
    arguments['method'] = 'update';
    arguments['context'] = context;
    arguments['view'] = view;

    sendAjaxCall(url, arguments, function(args) {
        //if(args['searchHtml'] != null) {
        //    $('.search-field', base).empty();
        //    $('.search-field', base).append(args['searchHtml']);
        //}
        if(args['paginationHtml'] != null) {
            $('.pagination-field', base).empty();
            $('.pagination-field', base).append(args['paginationHtml']);
        }
        if(args['sortHtml'] != null) {
            $('.sort-field', base).empty();
            $('.sort-field', base).append(args['sortHtml']);
        }

        $('.content-field', base).empty();
        $('.content-field', base).append(args['entitiesHtml']);
    }, function(args) {
        console.log(args);
    });
}

function getPagination(base) {
    var pagination = $('.pagination', base);
    if(pagination.length) {
        var array = [];
        array['offset'] = $('.active', pagination).data('offset');
        array['limit'] = pagination.data('limit');
        array['pageName'] = pagination.data('name');
        return array;
    }
    return null;
}

function getSort(base) {
    var sort = $('.sortable', base);
    if(sort.length) {
        var array = [];
        var active = $(':selected', sort);
        array['sortAttribute'] = active.data('attribute');
        array['sortValue'] = active.data('sorted');
        return array;
    }
    return null;
}

function getSearch(base) {
    var input = $('.search-value', base);
    if(input.length <= 0 || input.val() == '' || input.val() == null)
        return null;
    var value = input.val();

    var search = $('.search-form', base);
    if(search.length) {
        var object = {};
        object['value'] = value;
        var active = $('search-options .active', base);
        if(active.length) {
            return createSearchArray($(active), object, value);
        }
        else {
            $('.search-options a', base).each(function(index, value) {
                object = createSearchArray($(value), object, value);
            });
            return object;
        }
    }
    return null;
}

function getSearchValues(base) {
    var input = base.data('default-search-attributes');
    if(input.length <= 0 || input == '' || input == null)
        return null;

    return input;
}

function createSearchArray(activeElement, object) {
    if(activeElement.length) {
        var entity = activeElement.data('entity');
        object[entity] = activeElement.data('attribute');
    }
    return object;
}

function getCurrentOffset(object) {
    var panel = object.parents('.panel-body');
    var pagination = $('.pagination', panel);
    if(pagination.length == 0)
        return 0;
    var active = $('.active', pagination);
    return active.data('offset');
}

function getCurrentOrder(object) {
    var panel = object.parents('.panel-body');
    var sort = $('.sortable', panel);
    if(panel == null)
        return null;

    var active = $(':selected', sort);
    return {'attribute': active.data('attribute'), 'order': active.data('sorted')};
}

//A parent class should be the context
function rebuildPagination(paginationObject)
{
    var page = $(paginationObject);
    var url = page.data('url');
    var key = page.data('key');
    var method = 'reRenderPagination';
    var entity = page.data('entity');
    var maxPages = page.data('max-pages');
    var pageName = page.data('name');

    sendAjaxCall(url,
        {
            'method': method, 'ajax_key': key, 'offset': getCurrentOffset(page),
            'entity': entity, 'maxPages': maxPages, 'pageName': pageName
        },
        function(data) {
            $(page.parents('.pagination-container')).html(data['html']);
            removeLoadingScreen();
        },
        function(messages) {
            removeLoadingScreen();
        });
}

function addLoadingScreen(object){
    var loadingScreen = $("#loading");
    loadingScreen.css('top', $(object).position().top);
    loadingScreen.css('left', $(object).position().left);
    loadingScreen.css('height', $(object).height());

    var loadingScreenLogo = $('.loading-image', loadingScreen);
    loadingScreenLogo.css('top', '50%');
    loadingScreenLogo.css('left', '50%');

    loadingScreen.css('display', 'block');
}

function removeLoadingScreen() {
    $("#loading").css('display', 'none');
}



//Extensions
(function ($) {

    $.fn.filterByData = function (prop, val) {
        var $self = this;
        if (typeof val === 'undefined') {
            return $self.filter(
                function () { return typeof $(this).data(prop) !== 'undefined'; }
            );
        }
        return $self.filter(
            function () { return $(this).data(prop) == val; }
        );
    };

})(window.jQuery);