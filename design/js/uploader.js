// Uploader

$().ready(function() {

    var errors = "";

    $('#upload').mfupload({
        type        : 'patch,sh',
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
            $('#result_table_ee tbody').html('');
            $('#result_table_ce tbody').html('');
            // $('#result_table_pe tbody').html('');

            $('#uploaded').append("<div id='FILE" + result.fileno + "' class='files'>" + result.filename
                + "<div id='PRO" + result.fileno + "' class='progress'></div></div>");
        },

        loaded      : function(result) {
            $('#PRO' + result.fileno).remove();
            $('#FILE' + result.fileno).html('Uploaded: ' + result.filename + ' (' + result.size + ')');

            var output = '';
            for (var release in result.checkResults.ee) {
                var release = result.checkResults['ee'][release];

                output += '<tr><td';
                if (release.check_result == 'n/a') {
                    if (release.release_name == 'n/a') {
                        var column_content = '&nbsp;';
                    } else {
                        var column_content = release.release_name
                    }
                    output += ' colspan="2">' + column_content;
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
            $('#result_table_ee tbody').html(output);

            var output = '';
            for (var release in result.checkResults.ce) {
                var release = result.checkResults['ce'][release];

                output += '<tr><td';
                if (release.check_result == 'n/a') {
                    if (release.release_name == 'n/a') {
                        var column_content = '&nbsp;';
                    } else {
                        var column_content = release.release_name
                    }
                    output += ' colspan="2">' + column_content;
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
            $('#result_table_ce tbody').html(output);

            // var output = '';
            // for (var release in result.checkResults.pe) {
            //     var release = result.checkResults['pe'][release];
            //
            //     output += '<tr><td';
            //     if (release.check_result == 'n/a') {
            //         if (release.release_name == 'n/a') {
            //             var column_content = '&nbsp;';
            //         } else {
            //             var column_content = release.release_name
            //         }
            //         output += ' colspan="2">' + column_content;
            //     } else {
            //         output += '>' + release.release_name + '</td>';
            //         if (release.check_result == true) {
            //             output += '<td class="td_ok">Ok';
            //         } else {
            //             output += '<td class="td_fail">No';
            //         }
            //     }
            //     output += '</td></tr>';
            // }
            // $('#result_table_pe tbody').html(output);
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

    // $('#uploader_new button.plus').click(function(){
    //     var parentLi = $(this).parent();
    //     $(parentLi.clone(true)).insertAfter(parentLi);
    // })
    //
    // $('#uploader_new button.minus').click(function(){
    //     var parentLi = $(this).parent();
    //     var parentUl = $(parentLi).parent();
    //     if ($(parentUl).find('li').size() > 1) {
    //         $(parentLi).remove();
    //     }
    // })
    //
    // $('#uploader_new ul li input[type="file"]').change(function(){
    //     var value = $(this).val();
    //     var fileExtension = ['sh', 'patch'];
    //     if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
    //         alert("Only formats are allowed: " + fileExtension.join(', '));
    //         $(this).val('');
    //         return false;
    //     }
    // })

})
