$(document).ready(function() {
    var body = $('body');
    body.on('click', '.panel-body .pagination a', function(event) {
        updateReviewModals(this);
    });

    body.on('change', '.panel-body .sortable', function(event) {
        updateReviewModals(this);
    });

    body.on('click', '.modal-yes', function() {
        var id = $(this).data('id');
        var url = $(this).data('action');

        sendAjaxCall(url, {'ids': [id], 'method': 'deleteCourseByIds', 'ajax_key': 'CSAS1'}, function(){
            var target = $('.content-field div').filterByData('id', id);
            $(target).remove();
            var number = parseInt($('.total-courses').text());
            number -= 1;
            $('.total-courses').text(number);
            $('.modal').modal('hide');
        }, function(message) {
            console.log(message);
        });
    });
});

function updateReviewModals(caller) {
    var base = $($(caller).parents('.page-controls'));
    var pagination = getPagination(base);
    var sort = getSort(base);

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

    arguments['method'] = 'getReviewModals';
    arguments['ajax_key'] = 'CSAS1';
    arguments['context'] = context;

    sendAjaxCall(url, arguments, function(data) {
        $('.review-modals').empty();
        $('.review-modals').append(data['html']);
    }, function(message) {
        console.log(message);
    });
}