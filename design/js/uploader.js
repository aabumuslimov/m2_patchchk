// Uploader

$().ready(function() {

    var errors = "";

    $('#upload').mfupload({
        type        : 'patch,diff,sh',
        maxsize     : 15,
        post_upload : 'index.php?action=upload',
        folder      : '/tmp/_patchchk_uploads/',
        ini_text    : 'Drag your files to here or click',
        over_text   : 'Drop Here',
        over_col    : '#333',
        over_bkcol  : '#CCC',

        init        : function() {
            $('#uploaded').empty();
        },

        start       : function(result) {
            $('#results_div').html('');

            $('#uploaded').append("<div id='FILE" + result.fileno + "' class='files'>" + result.filename
                + "<div id='PRO" + result.fileno + "' class='progress'></div></div>");
        },

        loaded      : function(result) {
            $('#PRO' + result.fileno).remove();
            $('#FILE' + result.fileno).html('Uploaded: ' + result.filename + ' (' + result.size + ')');
            $('#upload').trigger('uploaded', [result]);
        },

        progress    : function(result) {
            $('#PRO' + result.fileno).css('width', result.perc + '%');
        },

        error       : function(error) {
            errors += error.filename + ': ' + error.err_des + "\n";
        },

        completed   : function() {
            if (errors != '') {
                alert(errors);
                errors = '';
            } else {
                $('#results_div').show();
                $('#legend_container_div').show();
            }
        }
    });
})
