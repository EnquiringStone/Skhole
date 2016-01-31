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
    //$('.sortable').on('change', function() {
    //    var sort = $(this);
    //    var url = sort.data('url');
    //    var entity = sort.data('entity');
    //    var attribute = $(sort.find(':selected')).data('attribute');
    //    var order = $(sort.find(':selected')).data('sorted');
    //    var method = sort.data('method');
    //    var key = sort.data('key');
    //
    //    sendAjaxCall(url, {'entity' : entity, 'attribute' : attribute, 'order' : order, 'offset': getCurrentOffset($(this)),
    //            'ajax_key' : key, 'method' : method}, function(data) {console.log('yep')});
    //});
});