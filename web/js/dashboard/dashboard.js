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
});