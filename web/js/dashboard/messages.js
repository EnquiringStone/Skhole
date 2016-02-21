/**
 * Created by johan on 24-Jan-16.
 */
$(document).ready(function() {
    var body = $('body');
    body.on('click', '.panel-body .pagination a', function(event) {
        updateReviewModals(this);
    });

    body.on('change', '.panel-body .sortable', function(event) {
        updateReviewModals(this);
    });

    $('.remove-selected-messages').on('click', function(event) {
        if(ids.length == 0) {
            $('#exceptionNoneSelected').modal('show');
        } else {
            $('#removeMessagesModal').modal('show');
        }
    });

    $('.read-selected-messages').on('click', function() {
        if(ids.length == 0) {
            $('#exceptionNoneSelected').modal('show');
        } else {
            var url = $('.panel-body').data('url');
            sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'readById', 'ids': ids}, function(data) {
                $.each(ids, function(index, item) {
                    readById(item);
                });
                $(':checkbox').prop('checked', false);
                ids = [];
            }, function(message) {

            });
        }
    });

    body.on('change', '.content-field input[type="checkbox"]', function() {
        var id = getMessageId(this);
        if (this.checked) {
            ids.push(id);
        } else {
            var index = ids.indexOf(id);
            if (index > -1) {
                ids.splice(index, 1);
            }
        }
    });

    $('.modal-yes', '#removeMessagesModal').on('click', function(event) {
        if(ids.length > 0) {
            var url = $('.panel-body').data('url');
            sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'deleteById', 'ids': ids}, function(data) {
                $.each(ids, function(index, item) {
                    var row = $('tr').filterByData('message-id', item);
                    row.remove();
                });
                $('#removeMessagesModal').modal('hide');
                var messages = parseInt($('.total-messages').text());
                $('.total-messages').text(messages - ids.length);
                ids = []; //reset
            }, function(message) {
                $('#removeMessagesModal').modal('hide');
            });
        }
    });

    body.on('click', '.remove-message', function() {
        if(selectedId > 0) {
            var url = $('.panel-body').data('url');
            sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'deleteById', ids: [selectedId]}, function(data) {
                var row = $('tr').filterByData('message-id', selectedId);
                row.remove();
                selectedId = 0;
                var messages = parseInt($('.total-messages').text());
                $('.total-messages').text(messages - 1);
                $('.modal').modal('hide');
            }, function(message) {
                $('#messagesModal').modal('hide');
            });
        }
    });

    body.on('click', '.content-field td .message-item', function(event) {
        event.preventDefault();
        var id = getMessageId(this);
        selectedId = id;

        var url = $('.panel-body').data('url');
        sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'readById', 'ids': [id]}, function(data) {
            readById(id);
        }, function(message) {

        });
    });
});

function updateReviewModals(caller) {
    var base = $($(caller).parents('.page-controls'));
    var pagination = getPagination(base);
    var sort = getSort(base);
    var defaultSearchValues = getSearchValues(base);

    var url = base.data('url');
    var context = base.data('context');
    var arguments = {};

    if(pagination != null) {
        arguments['offset'] = pagination['offset'];
        arguments['limit'] = pagination['limit'];
    }
    if(sort != null) {
        arguments['sortAttribute'] = sort['sortAttribute'];
        arguments['sortValue'] = sort['sortValue'];
    }
    if(defaultSearchValues != null) {
        arguments['defaultValues'] = defaultSearchValues;
    }

    arguments['method'] = 'getMessageModals';
    arguments['ajax_key'] = 'MAS1';
    arguments['context'] = context;

    sendAjaxCall(url, arguments, function(data) {
        $('.message-modals').empty();
        $('.message-modals').append(data['html']);
    }, function(message) {
        console.log(message);
    });
}

var ids = [];
var modalDateResetValue;
var selectedId = 0;

function getMessageId(object) {
    return $($(object).closest('tr')).data('message-id');
}

function readById(id) {
    var row = $('.content-field tr').filterByData('message-id', id);
    var title = $('.message-title', row);
    var text = $('b', title).text();
    if(text.length > 0) {
        $('b', title).remove();
        title.text(text);
    }
}