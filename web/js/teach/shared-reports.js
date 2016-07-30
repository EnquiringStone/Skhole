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
});

function getDataUrl() {
    return $('#mentor-requests').data('url');
}

function substractMentorRequest() {
    var element = $('.mentor-requests-count');

    element.text(parseInt(element.text()) - 1);
}