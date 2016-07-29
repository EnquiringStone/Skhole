$(document).ready(function() {
    var body = $('body');
    var hasChanged = false;

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

    body.on('click', '.save-questions', function (event) {
        event.preventDefault();
        saveAnswers(function () {
            var url = $('.next-page').attr('href');
            goToUrl(url);
        });
    });

    body.on('click', 'a', function (event) {
        event.preventDefault();
        var destinationUrl = $(this).attr('href');
        if(hasChanged == false) {
            goToUrl(destinationUrl);
            return;
        }
        saveAnswers(function () {
            goToUrl(destinationUrl);
        });
    });

    body.on('change', 'textarea', function () {
        hasChanged = true;
    });

    body.on('change', 'input[type="checkbox"]', function () {
        hasChanged = true;
    });

    body.on('click', '.check-report-completion', function () {
        var data = {};
        var study = $('#study');
        data['courseId'] = study.data('course-id');
        data['ajax_key'] = 'STAS1';
        data['method'] = 'validateReport';

        var goUrl = $(this).data('url');

        sendAjaxCall(study.data('url'), data, function () {
            goToUrl(goUrl);
        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
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

function saveAnswers(callBack) {
    var data = {};
    data['multipleChoice'] = addMultipleChoice();
    data['openQuestion'] = addOpenQuestions();
    if($.isEmptyObject(data['multipleChoice']) && $.isEmptyObject(data['openQuestion'])){
        callBack();
        return;
    }

    data['ajax_key'] = 'STAS1';
    data['method'] = 'saveAnswers';

    var study = $('#study');
    data['pageId'] = $('#page-id').val();
    data['courseId'] = study.data('course-id');

    var url = study.data('url');

    sendAjaxCall(url, data, callBack, function (error) {
        hasChanged = false;
        showAjaxErrorModal(error['responseJSON']['html']);
    });
}

function addOpenQuestions() {
    var data = {};
    $('.open-question').each(function () {
        var question = $(this);
        var questionId = question.data('question-id');
        var answer = $('.answer', question).val();
        if(answer != null && answer != '')
            data[questionId] = answer;
    });
    return data;
}

function addMultipleChoice() {
    var data = {};
    $('.multi-question').each(function () {
        var item = $(this);
        var specifiedAnswers = [];
        $('.answer', item).each(function () {
            var answer = $(this);
            if(answer.is(':checked')) {
                specifiedAnswers.push(answer.data('answer-id'));
            }
        });
        if(specifiedAnswers.length > 0) {
            var questionId = item.data('question-id');
            data[questionId] = specifiedAnswers;
        }
    });
    return data;
}

function getUserReviewData(context, method) {
    return {id: context.data('id'), ajax_key: 'STAS1', method: method, url: context.data('url'), rating: $('#modal-stars').attr('data-difficulty'), remarks: $('.remarks').val()};
}