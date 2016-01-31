/**
 * Created by johan on 24-Jan-16.
 */
$(document).ready(function() {
    modalDateResetValue = $('.messages-send-date-time', $('.messagesModal'))[0].innerText;
    $('.messages').on('change', ':checkbox', function () {
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

    $("body").on('click', '.messageItem', function() {
        $("div#messagesModal").modal();
    });

    $('.messages').on('click', '.messageItem', function(event) {
        clearMessages();
        var modal = $('.messagesModal');
        var data = $($(this).closest('td'));
        var id = getMessageId(this);

        $('.messages-send-date-time', modal).html(' ' + data.data('send-date'));
        $('.messages-sender-name', modal).html(data.data('sender'));
        $('.messages-title', modal).html(data.data('title'));
        $('.messages-contents', modal).html(data.data('contents'));
        $('.messages-id', modal).data('message-id', id);
        selectedId = id;

        var url = $('.panel-body').data('url');
        sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'deleteById', 'ids': [id]}, function(data) {
            readById(id);
        }, function(message) {

        });
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

    $('.sortable').on('change', function() {
        addLoadingScreen($(this).parents('.panel-body'));
        onSortChange(this, function(data) {
            clearTable();
            $.each(data, function(index, item) {
                $('.messages tr:last').after(createRow(item));
            });
            ids = []; //reset
            removeLoadingScreen();
        }, function(message) {
            removeLoadingScreen();
        });
    });

    $('.panel-body').on('click', '.pagination a', function(event) {
        event.preventDefault();
        addLoadingScreen($(this).parents('.panel-body'));
        onPaginationClick(this, function(data) {
            clearTable();
            $.each(data, function(index, item) {
                $('.messages tr:last').after(createRow(item));
            });
            rebuildPagination($('.pagination', '.panel-body'));
            ids = [];
        }, function(message) {
            removeLoadingScreen();
        });
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
                console.log('test');
                $('#removeMessagesModal').modal('hide');
            });
        }
    });

    $('.remove-message', '#messagesModal').on('click', function(event) {
        if(selectedId > 0) {
            var url = $('.panel-body').data('url');
            sendAjaxCall(url, {'ajax_key': 'MAS1', 'method': 'deleteById', ids: [selectedId]}, function(data) {
                var row = $('tr').filterByData('message-id', selectedId);
                row.remove();
                selectedId = 0;
                var messages = parseInt($('.total-messages').text());
                $('.total-messages').text(messages - 1);
                $('#messagesModal').modal('hide');
            }, function(message) {
                $('#messagesModal').modal('hide');
            });
        }

    });
});

var ids = [];
var modalDateResetValue;
var selectedId = 0;

function clearMessages() {
    var modal = $('.messagesModal');
    $('.modal-variable', modal).html('');
}

function getMessageId(object) {
    return $($(object).closest('tr')).data('message-id');
}

function createRow(entity) {
    var titleText = entity['isRead'] ? entity['title'] : "<b>"+ entity['title'] +"</b>";

    return  "<tr data-message-id='"+ entity['id'] +"'>" +
                "<td><input type='checkbox' /></td>" +
                "<td data-send-date='"+ entity['sendDate'] +"' data-title='"+entity['title']+"' data-contents='"+entity['contents']+"' data-sender='"+ entity['sender'] +"'>" +
                "<a class='messageItem' data-target='#messageModal'>" +
                    "<span class='text-muted clickable'>"+ titleText +"</span>" +
                "</a>" +
                "<span class='pull-right text-muted small'><em>"+ entity['timeBetween'] +"</em></span>" +
                "</td>" +
            "</tr>";
}

function clearTable() {
    $('.messages').find("tr:gt(0)").remove();
}

function readById(id) {
    var row = $('tr').filterByData('message-id', id);
    var title = $('.message-title', row);
    var text = $('b', title).text();
    if(text.length > 0) {
        $('b', title).remove();
        title.text(text);
    }
}