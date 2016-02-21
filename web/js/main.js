$(document).on('click', '.panel-heading span.clickable', function(e){
    var $this = $(this);
    if(!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    }
});

$(document).ready(function() {
    var body = $('body');

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
        if(li.hasClass('active'))
            return;
        $('.active', pagination).removeClass('active');
        li.addClass('active');

        refreshPage(this, false, false);
    });

    body.on('change', '.sortable', function(event) {
        refreshPage(this, true, false);
    });
});