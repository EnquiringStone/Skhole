$(document).ready(function() {
    var body = $('body');
    var reportForm = $('#sendReport');
    var ajaxUrl = reportForm.data('url');

    reportForm.on('submit', function (event) {
        event.preventDefault();

        var data = {};

        $('.data-value', reportForm).each(function () {
            var obj = $(this);
            var attribute = obj.data('attribute');

            var value = obj.val();
            if(attribute == 'subject' && obj.is(':checked'))
                data[attribute] = obj.data('type');
            else if(attribute == 'name')
                data[attribute] = obj.data('id');
            else if(attribute == 'page')
                data[attribute] = obj.data('id');
            else if(attribute != 'subject')
                data[attribute] = value;
        });

        data['ajax_key'] = 'OAS1';
        data['method'] = 'sendReport';

        sendAjaxCall(ajaxUrl, data, function (args) {
            var thumbnail = $(reportForm.parents('.panel-body'));
            reportForm.remove();
            thumbnail.html(args['html']);
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });
});