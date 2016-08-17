$(document).ready(function() {
    var body = $('body');

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.modal-yes', function () {
        var item = $(this);
        var url = item.data('action');
        var reportId = item.data('id');
        var totalReports = $('.total-reports').text();

        sendAjaxCall(url, {'reportId': reportId, 'ajax_key': 'STAS1', 'method': 'removeReport'}, function () {
            $('.modal').modal('hide');
            var row = $('.panel-default').filterByData('report-id', reportId);
            row.remove();
            $('.total-reports').text(parseInt(totalReports) - 1);
        }, function (error) {
            $('.modal').modal('hide');
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.send-mentor-request', function () {
        var base = $($(this).parents('.modal-body'));
        var reportId = $(this).data('report-id');

        var mentorCodeElement = $('.input-mentor-code', base);
        var mentorCode = mentorCodeElement.val();
        if(mentorCode == '' || mentorCode == null) return;

        sendAjaxCall($('.page-controls').data('url'), {'reportId': reportId, 'mentorCode': mentorCode, 'ajax_key': 'STAS1', 'method': 'shareReport'}, function () {
            $('.modal').modal('hide');
            mentorCodeElement.val('');
        }, function (error) {
            $('.modal').modal('hide');
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });
});
