$(document).ready(function() {
    var body = $('body');

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.accept-mentor-request', function () {
        var row = $($(this).parents('.row'));
        var id = row.data('id');

        sendAjaxCall(getDataUrl(), {'id': id, 'ajax_key': 'SRAS1', 'method': 'acceptMentorRequest'}, function () {
            row.remove();
            substractMentorRequest();
        //    TODO Update other panel
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.decline-mentor-request', function () {
        var row = $($(this).parents('.row'));
        var id = row.data('id');

        sendAjaxCall(getDataUrl(), {'id': id, 'ajax_key': 'SRAS1', 'method': 'declineMentorRequest'}, function () {
            row.remove();
            substractMentorRequest();
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.get-all-reports', function () {
        var base = $('#mentor-overview');
        var row = $($(this).parents('.user-row'));
        var div = $('.table-contents', row);

        if(div.html() != '')
        {
            div.html('');
            return;
        }

        sendAjaxCall(base.data('url'), {'userId': row.data('user-id'), 'ajax_key': 'SRAS1', 'method': 'getAllReports'}, function (data) {

            div.html(data['html']);
            refreshStarrrs(div);
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.save-report-changes', function () {
        var tr = $($(this).parents('tr'));
        var base = $('#mentor-overview');

        var rating = $('.starrr', tr).attr('data-difficulty');
        var hasRevised = $('input[type=checkbox]', tr).prop('checked');

        sendAjaxCall(base.data('url'), {'reportId': tr.data('report-id'), 'ajax_key': 'SRAS1', 'method': 'saveReportChanges', 'rating': rating, 'hasRevised': hasRevised}, function () {

        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.modal-yes', function () {
        var base = $('#mentor-overview');
        var button = $(this);
        var action = button.data('action');
        var id = button.data('id');
        var data = {};
        data['id'] = id;
        data['ajax_key'] = 'SRAS1';

        if(action == 'Remove')
        {
            data['method'] = 'removeReport';

            sendAjaxCall(base.data('url'), data, function (data) {
                $('.modal').modal('hide');
                setTimeout(function () {
                    if(data['lastRow'] == 'yes')
                    {
                        removeUser(data['userId']);
                    }
                    else
                    {
                        var row = $('tr').filterByData('report-id', id);
                        row.remove();
                    }
                }, 1000);
            }, function (error) {
                $('.modal').modal('hide');
                showAjaxErrorModal(error['responseJSON']['html']);
            });
        }
        else if(action == 'RemoveAll')
        {
            data['method'] = 'removeAllReports';

            sendAjaxCall(base.data('url'), data, function () {
                $('.modal').modal('hide');
                removeUser(id);
            }, function (error) {
                $('.modal').modal('hide');
                showAjaxErrorModal(error['responseJSON']['html']);
            })
        }
    });
});

function getDataUrl() {
    return $('#mentor-requests').data('url');
}

function substractMentorRequest() {
    var element = $('.mentor-requests-count');

    element.text(parseInt(element.text()) - 1);
}

function refreshStarrrs(context) {
    $('.starrr', context).each(function() {
        var element = $(this);
        var rating = element.attr('data-difficulty');
        element.starrr({
            rating: rating,
            numStars: 5
        });
    });
}

function removeUser(userId) {
    var userRow = $('.user-row').filterByData('user-id', userId);
    userRow.remove();
    var element = $('.total-users-count');

    element.text(parseInt(element.text()) - 1);
}