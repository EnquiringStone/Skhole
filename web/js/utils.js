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

function goToUrl(url) {
    window.location.href = url;
}

function refreshPage(caller, resetPagination, resetSort, customFunction) {
    var base = $($(caller).parents('.page-controls'));

    var entity = base.data('entity');
    var url = base.data('url');
    var context = base.data('context');
    var view = base.data('view');

    var arguments = {};

    var pagination = getPagination(base);
    var sort = getSort(base);
    var defaultSearchValues = getSearchValues(base);
    var search = getSearch(base);


    if(pagination != null) {
        if(!resetPagination) {
            arguments['offset'] = pagination['offset'];
            arguments['limit'] = pagination['limit'];
        }
        arguments['pageName'] = pagination['pageName'];
    } else {
        var page = base.data('pagination-name');
        if(page == undefined || page == '' || page == null)
            arguments['pageName'] = 'empty';
        else
            arguments['pageName'] = page;
    }
    if(sort != null && !resetSort) {
        arguments['sortAttribute'] = sort['sortAttribute'];
        arguments['sortValue'] = sort['sortValue'];
    }
    if(defaultSearchValues != null) {
        arguments['searchValues'] = defaultSearchValues;
    }
    if(search != null) {
        arguments['search'] = search['searchParams'];
        arguments['correlation'] = search['correlation'];
    }

    arguments['entity'] = entity;
    arguments['ajax_key'] = 'PCAS1';
    arguments['method'] = 'update';
    arguments['context'] = context;
    arguments['view'] = view;

    sendAjaxCall(url, arguments, function(args) {
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
        if(customFunction != null)
            customFunction(args);
    }, function(args) {
        //console.log(args);
    });
}

function getPagination(base) {
    var pagination = $('.pagination', base);
    var clicked = $('.clicked', pagination);
    if(pagination.length && clicked.length) {
        var array = [];
        array['offset'] = clicked.data('offset');
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
    var searchEnabled = $('.search-enabled', base);
    if(searchEnabled.length <= 0)
        return null;

    var correlation = searchEnabled.data('correlation');

    var array = {};
    $('.search-value', base).each(function(index, item) {
        var obj = $(item);
        var entity = obj.data('entity');
        var attributes = obj.data('attributes').split(',');
        var value = obj.val();
        var multipleValues = obj.data('has-multiple');

        if(value == null || value == '')
            return;

        if(multipleValues == '1') {
            value = value.split(',');
        }

        if(!(entity in array))
            array[entity] = {};

        for(var i = 0; i < attributes.length; i++) {
            array[entity][attributes[i]] = value;
        }
    });

    return {'searchParams': array, 'correlation': correlation};
}

function getSearchValues(base) {
    var input = base.data('default-search-attributes');
    if(input == undefined || input == '' || input == null|| input.length <= 0)
        return null;
    return input;
}

//TODO use this to show the error message from the ajax call
function showAjaxErrorModal(errorModalHtml) {
    $('.modal').modal('hide');
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