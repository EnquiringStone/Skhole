$(document).ready(function() {
    var body = $('body');
    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.modal-yes', function() {
        var url = $(this).data('url');
        var id = $(this).data('id');
        var args = {};
        args['id'] = id;
        args['method'] = 'removeCollectionItem';
        args['ajax_key'] = 'CCOAS1';

        sendAjaxCall(url, args, function(data) {
            var courseId = data['courseId'];
            $($('.panel').filterByData('id', courseId)).remove();
            $('.modal').modal('hide');
        }, function() {

        });
    });
});