$(document).ready(function() {
    var body = $('body');
    var contactForm = $('#contact-form');
    var ajaxUrl = contactForm.data('url');

    contactForm.on('submit', function (event) {
        event.preventDefault();

        var data = {};

        $('.data-value', contactForm).each(function () {
            var obj = $(this);
            var attribute = obj.data('attribute');

            data[attribute] = obj.val();
        });

        data['ajax_key'] = 'OAS1';
        data['method'] = 'sendContactEmail';

        sendAjaxCall(ajaxUrl, data, function (args) {
            var thumbnail = $(contactForm.parents('.thumbnail'));
            contactForm.remove();
            thumbnail.html(args['html']);
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });
});