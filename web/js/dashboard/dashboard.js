$(document).ready(function() {
    var body = $('body');

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.quick-search', function() {
        var value = $('.quick-search-value').val();
        if(value == null || value == '') return;

        var url = $(this).data('url');
        url += '?search='+value;
        goToUrl(url);
    });

    body.on('click', '.add-to-collection', function() {
        var context = $(this);
        var courseId = context.data('course-id');
        var url = context.data('url');

        var hrefUrl = context.data('href-url');
        var text = context.data('name');

        var args = {};
        args['method'] = 'addToCollection';
        args['ajax_key'] = 'CCOAS1';
        args['courseId'] = courseId;

        sendAjaxCall(url, args, function() {
            context.after('<a href="'+hrefUrl+'" class="btn btn-primary btn-sm">'+text+'</a>');
            context.remove();
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    });
});