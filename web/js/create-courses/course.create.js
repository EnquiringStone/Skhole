/**
 * Created by johan on 21-Feb-16.
 */
$(document).ready(function() {
    var body = $('body');
    var notSavedConfirmationModal = $('#notSaveConfirmationModal');

    $('.starrr').each(function() {
        var element = $(this);
        var rating = element.data('difficulty');
        if(rating == 0) {
            element.attr('data-difficulty', 3);
            rating = 3;
        }
        element.starrr({
            rating: rating,
            numStars: 5
        });
    });

    $('#select2-tags').select2({
        tags:true,
        tokenSeparators: [',', ' '],
        multiple: true
    });

    body.on('change', '.data-value', function() {
        hasChanged = true;
    });

    body.on('click', '.check-changes', function(event) {
        var href = $(this).attr('href');
        if(hasChanged) {
            event.preventDefault();
            showNotSavedConfirmationModal(href);
        }
    });

    body.on('change', '.card-introduction-change-youtube-text', function() {
        var regex = '(?:https?:\/\/)?(?:youtu\.be\/|(?:www\.)?youtube\.com\/watch(?:\.php)?\?.*v=)([a-zA-Z0-9\-_]+)';
        var input = $(this);
        var form = $(input.parents('.form-group'));
        console.log(form);
        if(!input.val().match(regex)) {
            if(!form.hasClass('has-error'))
                form.addClass('has-error');
        } else {
            if(form.hasClass('has-error'))
                form.removeClass('has-error');
        }
    });

    body.on('click', '.card-introduction-youtube-preview', function() {
        var input = $('.card-introduction-change-youtube-text');
        var form = $(input.parents('.form-group'));
        var videoDiv = $('.card-introduction-youtube');
        var embed = '';

        videoDiv.empty();

        if(form.hasClass('has-error')) {
            var errorText = videoDiv.data('error-text');
            videoDiv.append('<p>'+errorText+'</p>');
        } else {
            var value = input.val();
            if(value.indexOf('watch') >= 0) {
                //https://www.youtube.com/watch?v=ftLIR5AjqMs
                var split = value.split('?v=');
                var last = split[split.length - 1];
                embed = 'https://www.youtube.com/embed/'+last;
            }
            else {
                //https://youtu.be/ftLIR5AjqMs
                var slashes = value.split('/');
                var url = slashes[slashes.length - 1];

                embed = 'https://www.youtube.com/embed/' + url;
            }

            var html = '<div class="embed-responsive embed-responsive-4by3">' +
                '<iframe class="embed-responsive-item" allowfullscreen src="'+embed+'"' +
                '</div>';

            videoDiv.append(html);
        }
    });

    notSavedConfirmationModal.on('click', '.modal-yes', function() {
        var url = $('.url', notSavedConfirmationModal).val();
        var ajaxBody = $('.ajax-body');
        createSaveAjaxCall(url, ajaxBody.data('url'), ajaxBody.data('id'));
    });
    notSavedConfirmationModal.on('click', '.modal-no', function() {
        window.location.href = $('.url', notSavedConfirmationModal).val();
    });

    body.on('click', '.save-view', function(event) {
        event.preventDefault();
        var saveButton = $(this);
        var nextView = saveButton.data('next-view');
        createSaveAjaxCall(nextView);
    });

    body.on('click', '.save-teacher', function() {
        var url = $('.ajax-body').data('url');
        saveTeacherModal($(this));
    });

    body.on('click', '.save-provider', function() {
        saveCourseProvider($(this));
    });

    body.on('click', '.modal-yes', function() {
        var action = $(this).data('action');

        if(action == 'remove teacher') {
            removeTeacher($($(this).parents('.modal')), $(this).data('id'));
        }
        else if(action == 'remove announcement') {
            removeCourseAnnouncement($($(this).parents('.modal')), $(this).data('id'));
        } else if(action == 'remove provider') {
            removeProvider($($(this).parents('.modal')), $(this).data('id'));
        } else if(action == 'remove course') {
            removeCourse($(this).data('id'));
        } else if(action == 'remove page') {
            removePage($($(this).parents('.modal')), $(this).data('id'));
        }
    });

    body.on('click', '.save-announcement', function() {
        var parent = $($(this).parents('.modal'));
        saveCourseAnnouncement(parent);
    });

    bindFileUpload();

    bindSortable();

    body.on('click', '.add-more-question-inputs-button', function() {
        addAnswerHtml();
    });

    body.on('click', '.save-multiple-choice', function() {
        saveMultipleChoice($($(this).parents('.modal')));
    });

    body.on('click', '.course-add-custom-questions', function() {
        var titleElement = $('.title-exercise-page', $($(this).parents('.modal')));
        var titleOfPage = titleElement.val();
        if(titleOfPage == null || titleOfPage == '') {
            $(titleElement.parents('.form-group')[0]).addClass('has-error');
            return;
        }
        addExercisePage(titleOfPage);
    });

    body.on('click', '.load-question', function() {
        loadQuestion($(this).data('type'), $(this).data('question-id'));
    });

    body.on('click', '.remove-extra-answer', function(event) {
        event.preventDefault();

        var answerId = $(this).data('answer-id');
        if(answerId > 0)
            removeExtraAnswer(answerId);

        var parent = $($(this).parents('.form-group')[0]);
        var number = $('.form-control', parent).data('answer-order');
        parent.remove();

        var checkbox = $($($('input[type="checkbox"]').filterByData('answer-order', number)).parents('label')[0]);
        checkbox.remove();

        var answers = parseInt($('.answer-amount').val());
        $('.answer-amount').val(answers - 1);

        $('.form-group', '.answers').each(function(index, item) {
            $('.control-label', $(item)).html(orderToAlphabet(index + 1));
            var input = $('.form-control', $(item));
            input.attr('data-answer-order', index + 1);
        });

        $('label', '.correct-answers').each(function(index, item) {
            var input = $('.data-value', $(item));
            input.attr('data-answer-order', index + 1);
            var html = input[0].outerHTML;
            $(item).html(html + ' ' + orderToAlphabet(index + 1));
        });
    });
    body.on('click', '.save-custom-question', function() {
        saveCustomQuestion('');
    });
    body.on('click', '.remove-custom-question', function() {
        removeCustomQuestion($(this).data('question-id'));
    });
    body.on('click', '.select-questions-overview', function() {
        loadQuestion('overview', -1);
    });

    body.on('click', '.publish-course', function() {
        publishCourse($(this).data('course-overview-url'));
    });

    body.on('click', '.remove-resource', function() {
        removeResourceUrl($(this));
    });

    body.on('click', '.add-resource-url', function() {
        addResourceLink($(this));
    });

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });
});

var hasChanged = false;
var addAnswersButtonHtml = '';

function publishCourse(redirectUrl) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['id'] = id;
    args['method'] = 'publishCourse';
    args['ajax_key'] = 'CCAS1';

    sendAjaxCall(url, args, function() {
        goToUrl(redirectUrl);
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function removeExtraAnswer(answerId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['answerId'] = answerId;
    args['id'] = id;
    args['method'] = 'removeExtraAnswer';
    args['ajax_key'] = 'CCAS1';
    args['pageId'] = $('.exercise-page-id').val();

    sendAjaxCall(url, args, function() {

    }, function() {

    });
}

function removeCustomQuestion(questionId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['id'] = id;
    args['ids'] = [questionId];
    args['method'] = 'removeCustomQuestions';
    args['ajax_key'] = 'CCAS1';
    args['pageId'] = $('.exercise-page-id').val();

    sendAjaxCall(url, args, function(data) {
        var div = $('.load-question-type-div');
        div.empty();
        div.append(data['html']);
        hasChanged = false;
    }, function() {

    });
}

function saveCustomQuestion(redirectUrl) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');
    var args = {};
    var answers = {};

    $('.data-value').each(function() {
        var obj = $(this);
        var attribute = obj.data('value-name');

        if(attribute == 'answers' || attribute == 'correctAnswers') {
            var order = obj.data('answer-order');
            var answerId = obj.data('answer-id');
            var name = attribute == 'answers' ? 'answer' : 'correct';

            if(answers[order] != undefined) {
                answers[order][name] = name == 'correct' ? obj.is(':checked') : obj.val();
            } else {
                answers[order] = {};
                answers[order]['answerId'] = answerId;
                answers[order][name] = name == 'correct' ? obj.is(':checked') : obj.val();
            }
        } else {
            args[attribute] = obj.val();
        }
    });

    args['answers'] = answers;
    args['id'] = id;
    args['ajax_key'] = 'CCAS1';
    args['method'] = 'saveCustomQuestion';

    sendAjaxCall(url, args, function(data) {
        var div = $('.load-question-type-div');
        div.empty();
        div.append(data['html']);
        hasChanged = false;
        bindSortable();
        if(redirectUrl != null && redirectUrl != '')
            goToUrl(redirectUrl);
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function loadQuestion(type, questionId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['id'] = id;
    args['method'] = 'loadQuestion';
    args['ajax_key'] = 'CCAS1';
    args['type'] = type;
    args['questionId'] = questionId;
    args['pageId'] = $('.exercise-page-id').val();

    sendAjaxCall(url, args, function(data) {
        var div = $('.load-question-type-div');
        div.empty();
        div.append(data['html']);
        bindSortable();
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function bindSortable() {
    $('#sortable').sortable({
        stop: doSort
    });
    $('#sortable').disableSelection();
}

function addExercisePage(titleOfPage) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');
    var args = {};

    args['id'] = id;
    args['method'] = 'addCustomExercisePage';
    args['ajax_key'] = 'CCAS1';
    args['title'] = titleOfPage;

    sendAjaxCall(url, args, function(data) {
        goToUrl(data['url']);
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function saveMultipleChoice(contextModal) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');
    var questionId = $('.modal-body', contextModal).data('question-id');

    var args = {};
    var answers = {};

    $('.data-value', contextModal).each(function(item) {
        var obj = $(this);
        var attribute = obj.data('value-name');

        if(attribute == 'answers' || attribute == 'correctAnswers') {
            var order = obj.data('answer-order');
            var answerId = obj.data('answer-id');
            var name = attribute == 'answers' ? 'answer' : 'correct';

            if(answers[order] != undefined) {
                answers[order][name] = name == 'correct' ? obj.is(':checked') : obj.val();
            } else {
                answers[order] = {};
                answers[order]['answerId'] = answerId;
                answers[order][name] = name == 'correct' ? obj.is(':checked') : obj.val();
            }
        } else {
            args[attribute] = obj.val();
        }
    });

    args['questionId'] = questionId;
    args['id'] = id;
    args['pageId'] = $('.exercise-page-id').val();
    args['answers'] = answers;
    args['method'] = 'saveMultipleChoice';
    args['ajax_key'] = 'CCAS1';

    sendAjaxCall(url, args, function(data) {
        contextModal.modal('hide');
        var baseModal = $('.clear-modal-values', contextModal);
        if(baseModal.length) {
            resetAddMultipleChoiceModal(contextModal);
        }
        $('.questions-overview').empty();
        $('.questions-overview').append(data['html']);
        $('.exercise-page-id').val(data['pageId']);
        hasChanged = false;
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}


function addAnswerHtml() {
    var answers = parseInt($('.answer-amount').val());
    if(answers >= 5)
        return;
    $('.answer-amount').val(answers + 1);
    answers ++;

    var html = '<div class="form-group added-answer"><label class="col-sm-2 control-label">'+orderToAlphabet(answers)+'</label><div class="col-sm-9">' +
        '<input type="text" class="form-control data-value" data-value-name="answers" data-answer-id="-1" data-answer-order="'+answers+'">' +
        '</div><div class="col-sm-1"><a class="remove-extra-answer" href="#" data-answer-id="-1"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></div></div>';

    $('.answers').append(html);

    $('.correct-answers').append('<label class="added-answer"><input type="checkbox" class="data-value" data-answers-id="-1" data-value-name="correctAnswers" data-answer-order="'+answers+'"> ' +
        '' + orderToAlphabet(answers) +
        '</label> ');
}

function resetAddMultipleChoiceModal(modal) {
    $('.answer-amount', modal).val(2);
    $('.answers', modal).after(addAnswersButtonHtml);
    addAnswersButtonHtml = '';
    $('.added-answer', modal).remove();
    $('input', modal).val('');
    $('textarea', modal).val('');
}

function orderToAlphabet(number) {
    var alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    return alphabet[number - 1].toUpperCase();
}

function removePage(modal, pageId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['id'] = id;
    args['method'] = 'removePages';
    args['ajax_key'] = 'CCAS1';
    args['ids'] = [pageId];

    sendAjaxCall(url, args, function() {
        modal.modal('hide');
        var contextDiv = $($('li').filterByData('page-id', pageId));
        contextDiv.remove();
        refreshOrder($('#sortable'));
    }, function() {

    });
}

function doSort(event, ui) {
    var context = $(this);
    var name = context.data('name');
    var ajaxBody = $('.ajax-body');
    var sendUrl = ajaxBody.data('url');
    var id = ajaxBody.data('id');
    var item = $(ui['item']);

    var args = {};
    args['id'] = id;
    args['ajax_key'] = 'CCAS1';

    if(name == 'pages') {
        args['pageId'] = item.data('page-id');
        args['method'] = 'updatePageOrder';
    }
    else if(name == 'questions')
    {
        args['questionId'] = item.data('question-id');
        args['method'] = 'updateQuestionOrder';
    }
    args['order'] = $('li', context).index(item) + 1;
    if(args['order'] == item.attr('data-original-order'))
        return; //Nothing changed

    sendAjaxCall(sendUrl, args,
        function(){
            refreshOrder(context);
        },
        function(error){
        }
    );
}

function refreshOrder(context)
{
    $('li', context).each(function(index, item) {
        var li = $(item);
        $('.page-overview-order', li).html(index + 1);
        li.attr('data-original-order', index + 1);
    });
}

function removeCourse(sendUrl) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};
    args['id'] = id;
    args['method'] = 'removeCourses';
    args['ajax_key'] = 'CCAS1';
    args['ids'] = [id];

    sendAjaxCall(url, args, function() {
        goToUrl(sendUrl);
    }, function() {

    });
}

function removeProvider(modal, providerId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var method = 'removeCourseProviders';
    var key = 'CCAS1';

    var args = {};

    args['ids'] = [providerId];
    args['method'] = method;
    args['ajax_key'] = key;
    args['id'] = id;

    sendAjaxCall(url, args, function() {
        modal.modal('hide');
        var contextDiv = $($('li').filterByData('provider-id', providerId));
        contextDiv.remove();
    }, function(error) {
        console.log(error);
    });
}

function removeCourseAnnouncement(baseModal, announcementId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var method = 'removeCourseAnnouncement';
    var key = 'CCAS1';

    var args = {};

    args['ids'] = [announcementId];
    args['method'] = method;
    args['ajax_key'] = key;
    args['id'] = id;

    sendAjaxCall(url, args, function() {
        baseModal.modal('hide');
        var contextDiv = $($('ul').filterByData('announcement-id', announcementId));
        contextDiv.remove();
        hasChanged = false;
    }, function(error) {
        console.log(error);
    });
}

function removeTeacher(modal, teacherId) {
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var method = 'removeCourseTeachers';
    var key = 'CCAS1';

    var args = {};

    args['ids'] = [teacherId];
    args['method'] = method;
    args['ajax_key'] = key;
    args['id'] = id;

    sendAjaxCall(url, args, function() {
        modal.modal('hide');
        var contextDiv = $($('li').filterByData('teacher-id', teacherId));
        contextDiv.remove();
        hasChanged = false;
    }, function(error) {
        console.log(error);
    });
}

function bindFileUpload() {
    $('.teacher-picture').fileupload({
        url: $('.ajax-body').data('picture-upload-path'),
        sequentialUploads: false,
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,
        done: function(e, data) {
            var picture = $('.uploaded-teacher-picture');
            picture.val(data['_response']['result'][0]);

            var preview = $('.teacher-picture-preview');

            var html = '<img src="/'+window.location.pathname.split('/')[1]+'/web/'+ picture.val()+'" class="teacher-picture-preview-img">';
            preview.empty();
            preview.append(html);
        }
    });

    $('.teacher-picture').bind('fileuploadfail', function(e, data) {
        $('.modal').modal('hide');
        var error = data['_response']['jqXHR']['responseJSON']['html'];
        showAjaxErrorModal(error);
    });
}

function showNotSavedConfirmationModal(href) {
    var modal = $("#notSaveConfirmationModal");
    $('.url', modal).val(href);
    modal.modal('show');
}

function createSaveAjaxCall(url) {
    var ajaxBody = $('.ajax-body');
    var name = ajaxBody.data('name');
    var sendUrl = ajaxBody.data('url');
    var id = ajaxBody.data('id');
    if(name == 'course-information')
        saveCourseInformation(url, sendUrl, id);
    else if(name == 'card-introduction')
        saveCardIntroduction(url, sendUrl, id);
    else if(name == 'card-provider')
        saveCardProvider(url, sendUrl, id);
    else if(name == 'card-schedule')
        saveSchedule(url, sendUrl, id);
    else if(name == 'card-teacher')
        goToUrl(url); //Nothing to save here
    else if(name == 'course-announcement')
        goToUrl(url); //Nothing to save here
    else if(name == 'text-instruction' || name == 'text-video-instruction')
        saveInstruction(url, sendUrl, id);
    else if(name == 'questions') {
        saveCustomQuestion(url);
    }
    else if(name == 'course-resources') {
        saveResources(url, sendUrl, id);
    }
}

function addResourceLink(context) {
    var parent = $(context.parents('.thumbnail'));
    var method = 'addResourceLink';
    var key = 'CCAS1';
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');
    var resourceUrl = $('.data-value', parent).val();

    var args = {};
    args['method'] = method;
    args['ajax_key'] = key;
    args['type'] = context.data('type');
    args['resourceUrl'] = resourceUrl;
    args['id'] = id;

    sendAjaxCall(url, args, function() {
        $('.resource-url', parent).empty();
        $('.resource-url', parent).append(
            '<a href="'+resourceUrl+'" class="img-rounded" target="_blank">'+resourceUrl+'</a>'
        );
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function saveResources(url, sendUrl, id) {
    if(hasChanged) {
        var method = 'saveCourseResources';
        var key = 'CCAS1';

        var args = {};
        $('.data-value').each(function() {
            var obj = $(this);
            var attribute = obj.data('value-name');
            args[attribute] = obj.val();
        });
        args['ajax_key'] = key;
        args['method'] = method;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    }
    else {
        goToUrl(url);
    }
}

function removeResourceUrl(context) {
    var method = 'removeResourceUrl';
    var key = 'CCAS1';
    var url = $('.ajax-body').data('url');
    var id = $('.ajax-body').data('id');

    var args = {};

    args['method'] = method;
    args['ajax_key'] = key;
    args['type'] = context.data('type');
    args['id'] = id;

    sendAjaxCall(url, args, function() {
        var parent = $(context.parents('.thumbnail'));
        $('.resource-url', parent).empty();
        $('.data-value', parent).val('');
    }, function() {

    });
}

function saveInstruction(url, sendUrl, id) {
    if(hasChanged)
    {
        var method = 'saveCourseInstruction';
        var key = 'CCAS1';

        var args = {};
        $('.data-value').each(function(item) {
            var obj = $(this);
            var attribute = obj.data('value-name');
            args[attribute] = obj.val();
        });
        args['ajax_key'] = key;
        args['method'] = method;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    }
    else {
        goToUrl(url);
    }
}

function saveCourseAnnouncement(baseModal) {
    var ajaxBody = $('.ajax-body');
    var sendUrl = ajaxBody.data('url');
    var id = ajaxBody.data('id');

    var method = 'saveCourseAnnouncement';
    var key = 'CCAS1';

    var args = {};
    $('.data-value').each(function(item) {
        var obj = $(this);
        var attribute = obj.data('value-name');
        args[attribute] = obj.val();
    });
    args['ajax_key'] = key;
    args['method'] = method;
    args['id'] = id;

    sendAjaxCall(sendUrl, args, function(data) {
        var row = $('.course-announcements');
        if($('.no-announcements', row).length)
            $('.no-announcements', row).remove();

        row.append(data['html']);
        $('textarea', baseModal).val('');
        $('input', baseModal).val('');
        baseModal.modal('hide');
        hasChanged = false;
    }, function(error) {
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function saveCourseInformation(url, sendUrl, id) {
    if(hasChanged)
    {
        var method = 'saveCourseInformationValues';
        var key = 'CCAS1';

        var args = {};
        $('.data-value').each(function(item) {
            var obj = $(this);
            var attribute = obj.data('value-name');
            var value = obj.val();

            if(attribute == 'tags') {
                args['tags'] = value == null ? null : value.join(',');
            }
            else if(attribute == 'difficulty') {
                args['difficulty'] = obj.attr('data-difficulty');
            }
            else {
                args[attribute] = value;
            }
        });
        args['ajax_key'] = key;
        args['method'] = method;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    }
    else {
        goToUrl(url);
    }
}

function saveCardIntroduction(url, sendUrl, id) {
    if(hasChanged) {
        var method = 'saveCardIntroduction';
        var key = 'CCAS1';
        var args = {};
        $('.data-value').each(function() {
            var item = $(this);
            var attribute = item.data('value-name');

            args[attribute] = item.val();
        });
        args['ajax_key'] = key;
        args['method'] = method;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    }
    else {
        goToUrl(url);
    }
}

function saveCardProvider(url, sendUrl, id) {
    if(hasChanged) {
        var method = 'saveCardProvider';
        var key = 'CCAS1';
        var args = {};
        $('.data-value').each(function() {
            var item = $(this);
            var attribute = item.data('value-name');
            if(attribute == 'priorKnowledge')
                args[attribute] = item.val();
        });
        args['method'] = method;
        args['ajax_key'] = key;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    } else {
        goToUrl(url);
    }
}

function saveSchedule(url, sendUrl, id) {
    if(hasChanged) {
        var method = 'saveSchedule';
        var key = 'CCAS1';
        var args = {};
        $('.data-value').each(function() {
            var item = $(this);
            var attribute = item.data('value-name');

            args[attribute] = item.val();
        });
        args['method'] = method;
        args['ajax_key'] = key;
        args['id'] = id;

        sendAjaxCall(sendUrl, args, function() {
            goToUrl(url);
        }, function(error) {
            var json = error['responseJSON'];
            showAjaxErrorModal(json['html']);
        });
    } else {
        goToUrl(url);
    }
}

function saveCourseProvider(context) {
    var modal = $(context.parents('.modal'));
    var ajaxBody = $('.ajax-body');
    var url = ajaxBody.data('url');
    var id = ajaxBody.data('id');

    args = {};

    args['id'] = id;
    args['ajax_key'] = 'CCAS1';
    args['method'] = 'saveProvider';

    $('.data-value', modal).each(function() {
        var item = $(this);
        var attribute = item.data('value-name');

        args[attribute] = item.val();
    });

    sendAjaxCall(url, args, function(data) {
        modal.modal('hide');
        var baseModal = $('.clear-modal-providers-values', modal);
        if(baseModal.length) {
            $('input', baseModal).val('');
            $('textarea', baseModal).val('');
            if('html' in data) {
                $('.provider-rows').append(data['html']);
            }
        } else {
            var contextDiv = $($('li').filterByData('provider-id', data['id']));
            if(contextDiv.length) {
                $('.provider-name', contextDiv).empty();
                $('.provider-name', contextDiv).append(args['name']);
                $('.provider-description', contextDiv).empty();
                $('.provider-description', contextDiv).append(args['description']);
            }
        }
        var providerNotFoundText = $('.no-providers-found-text');
        if(providerNotFoundText.length) providerNotFoundText.remove();
        hasChanged = false;
    }, function(error) {
        modal.modal('hide');
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}

function saveTeacherModal(context) {
    var modal = $(context.parents('.modal'));
    var ajaxBody = $('.ajax-body');
    var url = ajaxBody.data('url');
    var id = ajaxBody.data('id');

    args = {};

    args['id'] = id;
    args['ajax_key'] = 'CCAS1';
    args['method'] = 'saveTeacher';

    $('.data-value', modal).each(function() {
        var item = $(this);
        var attribute = item.data('value-name');

        args[attribute] = item.val();
    });

    sendAjaxCall(url, args, function(data) {
        modal.modal('hide');
        var baseModal = $('.clear-modal-teachers-values', modal);
        if(baseModal.length) {
            $('textarea', baseModal).val('');
            $('input', baseModal).val('');
            if('html' in data) {
                $('.teacher-rows').append(data['html']);
                bindFileUpload();
            }
        } else {
            var contextDiv = $($('li').filterByData('teacher-id', data['id']));
            if(contextDiv.length) {
                $('.teacher-name', contextDiv).empty();
                $('.teacher-name', contextDiv).append(args['name']);
                $('.teacher-description', contextDiv).empty();
                $('.teacher-description', contextDiv).append(args['description']);

                if(args['pictureUrl'] != null || args['pictureUrl'] != '') {
                    $('.teacher-picture-row-image', contextDiv).empty();
                    $('.teacher-picture-row-image', contextDiv).append('<img src="/'+window.location.pathname.split('/')[1]+'/web/'+args["pictureUrl"]+'" class="media-object teacher-row-picture">');
                }
            }
        }
        var teacherNotFound = $('.no-teacher-found-text');
        if(teacherNotFound.length) teacherNotFound.remove();
        hasChanged = false;
    }, function(error) {
        modal.modal('hide');
        var json = error['responseJSON'];
        showAjaxErrorModal(json['html']);
    });
}