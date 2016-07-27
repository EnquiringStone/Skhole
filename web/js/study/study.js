$(document).ready(function() {
    var body = $('body');

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.add-quick-course-review', function () {
        var button = $(this);
        var id = button.data('course-id');
        var url = button.data('url');
        var rating = $('#star').attr('data-difficulty');

        if(rating == '' || rating == undefined) return;

        sendAjaxCall(url, {id: id, ajax_key: 'STAS1', method: 'addQuickCourseReview', rating: rating}, function () {
            button.hide();
            $('.add-normal-review').hide();
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.create-user-review', function () {
        var data = getUserReviewData($(this), 'addCourseReview');

        sendAjaxCall(data['url'], data, function () {
            $('.modal').modal('hide');
            $('.add-normal-review').hide();
            $('.add-quick-course-review').hide();
        }, function (error) {
            $('.modal').modal('hide');
            showAjaxErrorModal(error['responseJSON']['html']);
        })
    });

    body.on('click', '.update-user-review', function () {
        var data = getUserReviewData($(this), 'updateCourseReview');

        sendAjaxCall(data['url'], data, function () {
            $('.modal').modal('hide');
            $('.add-normal-review').hide();
            $('.add-quick-course-review').hide();
        }, function (error) {
            $('.modal').modal('hide');
            showAjaxErrorModal(error['responseJSON']['html']);
        })
    });

    $('.starrr').each(function() {
        var element = $(this);
        var rating = element.data('difficulty');
        element.starrr({
            rating: rating,
            numStars: 5
        });
    });
});

function getUserReviewData(context, method) {
    return {id: context.data('id'), ajax_key: 'STAS1', method: method, url: context.data('url'), rating: $('#modal-stars').attr('data-difficulty'), remarks: $('.remarks').val()};
}