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