$(function() {

    $('.chlog-del-show-data').click(function() {
        var $elem = $(this);
        var url = $elem.attr('href');
        $.get(url, function(response) {
            var $modal = $('#chlog-del-data');
            $modal.find('section').html(response);
            $modal.modal('show');
        });
        return false;
    });

});