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

            for (var groupName in result.check_results) {
                var groupResults = result.check_results[groupName];

                var output = '<table class="result_table">'
                    + '<thead>'
                        + '<td colspan="2">' + groupName + '</td>'
                    + '</thead>';

                for (var release in groupResults) {
                    var release = groupResults[release];

                    output += '<tr><td';
                    if (release.check_result == 'n/a') {
                        if (release.release_name == 'n/a') {
                            var columnContent = '&nbsp;';
                        } else {
                            var columnContent = release.release_name
                        }
                        output += ' colspan="2">' + columnContent;
                    } else {
                        output += '>' + release.release_name + '</td>';
                        if (release.check_result == true) {
                            output += '<td class="td_ok">Ok';
                        } else {
                            output += '<td class="td_fail">No';
                        }
                    }
                    output += '</td></tr>';
                }
                output += '</table>';

                $('#results_div').append(output);
            }
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
            }
        }
    });
})
