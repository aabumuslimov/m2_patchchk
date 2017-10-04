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
                        + '<tr class="header">'
                            + '<td colspan="3">' + groupName + '</td>'
                        + '</tr>'
                        + '<tr class="subheader">'
                            + '<td>Version</td>'
                            + '<td>EE</td>'
                            + '<td>Cloud</td>'
                        + '</tr>'
                    + '</thead>';

                for (var release in groupResults) {
                    var release = groupResults[release];

                    output += '<tr><td';
                    if (release.check_method == 'n/a') {
                        if (release.instance_name == 'n/a') {
                            var columnContent = '&nbsp;';
                        } else {
                            var columnContent = release.instance_name
                        }
                        output += ' colspan="3">' + columnContent;
                    } else {
                        var falseResultClass = 'td_fail';
                        for (var methodResult in release.check_method) {
                            if (release.check_method[methodResult] == true) {
                                falseResultClass = 'td_adaptation_required';
                                break;
                            }
                        }

                        output += '>' + release.instance_name + '</td>';
                        if (release.check_method['patch'] == true) {
                            output += '<td class="td_ok">Ok';
                        } else {
                            output += '<td class="' + falseResultClass + '">No';
                        }
                        if (release.check_method['git'] == true) {
                            output += '<td class="td_ok">Ok';
                        } else {
                            output += '<td class="' + falseResultClass + '">No';
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
