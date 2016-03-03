/**
 * Created by johan on 02-Dec-15.
 */
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

function sendAjaxCallForUpload(url, contents, callback) {
    $.post(url, contents, callback);
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

//TODO use this to show the error message from the ajax call
function showAjaxErrorModal(errorModalHtml) {
    var modalDiv = $('.ajax-error-modal');
    modalDiv.empty();
    modalDiv.append(errorModalHtml);
    $('#ajaxErrorMessageModal', modalDiv).modal('show');
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

if (!Object.keys) {
    Object.keys = function (obj) {
        var arr = [],
            key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                arr.push(key);
            }
        }
        return arr;
    };
}