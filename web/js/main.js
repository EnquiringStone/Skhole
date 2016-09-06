$(document).on('click', '.panel-heading span.clickable', function(e){
    collapseByChevronSpan($(this));
});

$(document).ready(function() {
    var body = $('body');
    var message = $('#unread-messages-count');
    var cookiePanel = $('#cookie-panel');
    if(message.length)
    {
        sendAjaxCall(message.data('url'), {'ajax_key': 'MAS1', 'method': 'getUnreadMessagesCount'}, function (data) {
            message.text(data['unreadMessages']);
        }, function () {
            message.text(0);
        });
    }

    body.on('click', '.btn-pref .btn', function() {
        var modal = $($(this).parents('.btn-pref'));
        $(".btn", modal).removeClass("btn-primary").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    body.on('click', '.search-panel .dropdown-menu a', function(event) {
        event.preventDefault();
        if($(this).hasClass('active'))
            return;

        var param = $(this).attr("href").replace("#","");
        var concept = $(this).text();
        $('.search-panel span#search_concept').text(concept);
        $('.input-group #search_param').val(param);

        var active = $('.search-options').find('.active');
        if($(active).length)
            $(active).removeClass('active');

        $(this).addClass('active');
    });

    body.on('click', '.pagination a', function(event) {
        event.preventDefault();
        var pagination = $($(this).parents('.pagination'));
        var li = $($(this).parent());
        if(pagination.data('disable')) {
            goToUrl($(this).attr('href'));
            return;
        }
        if(li.hasClass('active') || li.hasClass('disabled'))
            return;

        li.addClass('clicked');

        refreshPage(this, false, false);
    });

    body.on('change', '.sortable', function(event) {
        refreshPage(this, true, false);
    });

    cookiePanel.on('click', '.save-cookie-choice', function () {
        var url = cookiePanel.data('url');

        sendAjaxCall(url, {'ajax_key': 'cookie', 'method': 'acceptCookies'}, function () {

        }, function (error) {
            showAjaxErrorModal(error['responseJSON']['html']);
        });
    });

    body.on('click', '.load-login-spinner', function () {
        var modal = $($(this).parents('.modal'));
        addLoadingScreen(modal);
        modal.modal('hide');
    });
});

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