$(document).ready(function() {
    var body = $('body');
    var datePicker = $('#date-picker');
    var profilePictureUpload = $('#profile-picture-upload');

    setLocale();

    datePicker.datetimepicker({
        i18n: {
            nl: {
                months:[
                    'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'
                ],
                daysOfWeek:[
                    'Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'
                ]
            }
        },
        format:'d-m-Y',
        formatDate:'d-m-Y',
        timepicker:false
    });

    profilePictureUpload.fileupload({
        url: $('.picture-upload-path').val(),
        sequentialUploads: false,
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,
        done: function(e, data) {
            var picture = $('.uploaded-profile-picture');
            picture.val(data['_response']['result'][0]);

            var preview = $('.profile-picture-preview-img');
            preview.attr('src', '/'+window.location.pathname.split('/')[1]+'/web/'+ picture.val());
        }
    });

    profilePictureUpload.bind('fileuploadfail', function(e, data) {
        $('.modal').modal('hide');
        var error = data['_response']['jqXHR']['responseJSON']['html'];
        showAjaxErrorModal(error);
    });

    body.on('click', '.save-profile', function() {
        var modal = $($(this).parents('.modal'));
        var url = $(this).data('url');
        var args = {};
        args['method'] = 'updatePerson';
        args['ajax_key'] = 'PRAS1';
        args['context'] = 'SELF';

        var picture = '';
        $('.data-value', modal).each(function() {
            var obj = $(this);
            var key = obj.data('value-name');
            args[key] = obj.val();
            if(key == 'picture')
                picture = obj.val();
        });

        sendAjaxCall(url, args, function(data) {
            $('.modal').modal('hide');
            $('.profile-detail-buttons').empty();
            $('.profile-detail-buttons').append(data['html']);
            if(picture != null && picture != '') {
                var imagePath = '/' + window.location.pathname.split('/')[1] + '/web/' + picture;
                $('#profile-picture-header').attr('src', imagePath);
            }

        }, function(error) {
            $('.modal').modal('hide');
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    });

    body.on('click', '.save-education', function() {
        var context = $($(this).parents('.modal'));
        var url = $(this).data('url');

        var args = {};
        args['method'] = 'updateEducation';
        args['ajax_key'] = 'PRAS1';
        $('.data-value', context).each(function() {
            var obj = $(this);
            var key = obj.data('value-name');
            args[key] = obj.val();
        });
        args['context'] = 'SELF';

        sendAjaxCall(url, args, function(data) {
            $('.modal').modal('hide');
            $('.profile-education-buttons').empty();
            $('.profile-education-buttons').append(data['html']);
        }, function(error) {
            $('.modal').modal('hide');
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    });

    body.on('click', '.remove-user-picture', function () {
        var click = $(this);
        var context = $(click.parents('.modal'));
        var standardPictureUrl = click.data('standard-picture');
        var profilePicture = $('.profile-picture-preview-img', context);

        if (standardPictureUrl == profilePicture.attr('src')) return;

        var url = click.data('url');

        sendAjaxCall(url, {'ajax_key': 'PRAS1', 'method': 'removeProfilePicture'}, function () {
            profilePicture.attr('src', standardPictureUrl);
            $('#profile-picture-header').attr('src', standardPictureUrl);
            var picture = $('.uploaded-profile-picture');
            picture.val('');
        }, function (error) {
            context.modal('hide');
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    });
});

function setLocale() {
    var locale = window.location.pathname.split('/')[3];

    jQuery.datetimepicker.setLocale(locale);
}