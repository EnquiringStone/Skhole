/**
 * Created by johan on 21-Feb-16.
 */
$(document).ready(function() {
    var body = $('body');
    var notSavedConfirmationModal = $('#notSaveConfirmationModal');

    $('.starrr').each(function() {
        var element = $(this);
        var rating = element.data('difficulty');
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

    body.on('change', '.teacher-picture', function() {
        var file = this.files[0];
        var imageType = file.type;
        var preview = $('.teacher-picture-preview');
        preview.empty();
        //TODO http://blueimp.github.io/jQuery-File-Upload/basic-plus.html
        if(imageType == 'image/jpeg' || imageType == 'image/png' || imageType == 'image/jpg') {
            var reader = new FileReader();
            reader.onload = teacherImageIsLoaded;
            reader.readAsDataURL(file);
        } else {
            var error = $(this).data('error');
            preview.append('<p>'+error+'</p>');
        }
    });

    body.on('click', '.save-teacher', function() {
        //var reader = new FileReader();
        //reader.readAsText(pictureFile, 'UTF-8');
        //reader.onload = saveTeacherModal;
    });

});

function teacherImageIsLoaded(e) {
    var sendUrl = $('.ajax-body').data('picture-upload-path');

    sendAjaxCallForUpload(sendUrl, {data: e.target.result, name: 'teacher'}, function() {
        var preview = $('.teacher-picture-preview');
        var html = '<img src="'+ e.target.result+'" class="teacher-picture-preview-img">';
        preview.append(html);
    });
}

var hasChanged = false;

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
                args['tags'] = value.join(',');
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

function saveTeacherModal(event) {
    //TODO http://stackoverflow.com/questions/4006520/using-html5-file-uploads-with-ajax-and-jquery
    var modal = $($(this).parents('.modal'));
    var ajaxBody = $('.ajax-body');
    var sendUrl = ajaxBody.data('picture-upload-path');
    var url = ajaxBody.data('url');
    var id = ajaxBody.data('id');

    args = {};

    args['id'] = id;
    args['ajax_key'] = 'CCAS1';
    args['method'] = 'saveTeacher';

    $('.data-value').each(function() {
        var item = $(this);
        var attribute = item.data('value-name');

        args[attribute] = item.val();
    });

    //var fileName = $('.teacher-picture').files[0].name;
    //console.log(fileName);

    //sendAjaxCall(url, args, function() {
    //    modal.modal('hide');
    //    var baseModal = $('.clear-modal-teachers-values', modal);
    //    if(baseModal.length) {
    //        $('textarea', baseModal).val('');
    //        $('input', baseModal).val('');
    //        pictureFile = null;
    //    }
    //}, function(error) {
    //    modal.modal('hide');
    //    var json = error['responseJSON'];
    //    showAjaxErrorModal(json['html']);
    //});

}

function goToUrl(url) {
    window.location.href = url;
}


//Starsss
var __slice = [].slice;

(function($, window) {
    var Starrr;

    Starrr = (function() {
        Starrr.prototype.defaults = {
            rating: void 0,
            numStars: 5,
            change: function(e, value) {}
        };

        function Starrr($el, options) {
            var i, _, _ref,
                _this = this;

            this.options = $.extend({}, this.defaults, options);
            this.$el = $el;
            _ref = this.defaults;
            for (i in _ref) {
                _ = _ref[i];
                if (this.$el.data(i) != null) {
                    this.options[i] = this.$el.data(i);
                }
            }
            this.createStars();
            this.syncRating();
            this.$el.on('mouseover.starrr', 'span', function(e) {
                return _this.syncRating(_this.$el.find('span').index(e.currentTarget) + 1);
            });
            this.$el.on('mouseout.starrr', function() {
                return _this.syncRating();
            });
            this.$el.on('click.starrr', 'span', function(e) {
                hasChanged = true;
                var number = _this.$el.find('span').index(e.currentTarget) + 1;
                $($(this).parents('.starrr')).attr('data-difficulty', number);
                return _this.setRating(_this.$el.find('span').index(e.currentTarget) + 1);
            });
            this.$el.on('starrr:change', this.options.change);
        }

        Starrr.prototype.createStars = function() {
            var _i, _ref, _results;

            _results = [];
            for (_i = 1, _ref = this.options.numStars; 1 <= _ref ? _i <= _ref : _i >= _ref; 1 <= _ref ? _i++ : _i--) {
                _results.push(this.$el.append("<span class='glyphicon .glyphicon-star-empty'></span>"));
            }
            return _results;
        };

        Starrr.prototype.setRating = function(rating) {
            if (this.options.rating === rating) {
                rating = void 0;
            }
            this.options.rating = rating;
            this.syncRating();
            return this.$el.trigger('starrr:change', rating);
        };

        Starrr.prototype.syncRating = function(rating) {
            var i, _i, _j, _ref;

            rating || (rating = this.options.rating);
            if (rating) {
                for (i = _i = 0, _ref = rating - 1; 0 <= _ref ? _i <= _ref : _i >= _ref; i = 0 <= _ref ? ++_i : --_i) {
                    this.$el.find('span').eq(i).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
                }
            }
            if (rating && rating < 5) {
                for (i = _j = rating; rating <= 4 ? _j <= 4 : _j >= 4; i = rating <= 4 ? ++_j : --_j) {
                    this.$el.find('span').eq(i).removeClass('glyphicon-star').addClass('glyphicon-star-empty');
                }
            }
            if (!rating) {
                return this.$el.find('span').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
            }
        };

        return Starrr;

    })();
    return $.fn.extend({
        starrr: function() {
            var args, option;

            option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
            return this.each(function() {
                var data;

                data = $(this).data('star-rating');
                if (!data) {
                    $(this).data('star-rating', (data = new Starrr($(this), option)));
                }
                if (typeof option === 'string') {
                    return data[option].apply(data, args);
                }
            });
        }
    });
})(window.jQuery, window);