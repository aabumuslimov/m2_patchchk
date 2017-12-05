$().ready(function() {
    $('#legend_header_div')
        .mouseenter(function() {
            $('#legend_fold_icon').css('filter', 'brightness(1)');
        })
        .mouseleave(function() {
            $('#legend_fold_icon').css('filter', 'brightness(0.85)');
        });

    $('#legend_header_div').click(function () {
        $('#legend_body_div').toggle();
        $('#legend_fold_icon').toggleClass('rotate180');
    })
})
