$().ready(function () {
    $('#legend_header_div')
        .mouseenter(function () {
            $('#legend_fold_icon').css('filter', 'brightness(1)');
        })
        .mouseleave(function () {
            $('#legend_fold_icon').css('filter', 'brightness(0.85)');
        });

    $('#legend_header_div').click(function () {
        $('#legend_body_div').toggle();
        $('#legend_fold_icon').toggleClass('rotate180');
    })

    $('#upload').on('uploaded', function (e, result) {
        render_result(result);
    });

    var $form = $('#mqp_form'),
        $patchIdInput = $form.find('input#mqp_input_patch_id'),
        $patchIdError = $form.find('span#mqp_error_patch_id'),
        $resultContainer = $('#results_div'),
        $legendContainer = $('#legend_container_div');

    $form.submit(function (e) {
        e.preventDefault();
        var data = $form.serialize(),
            url = $form.attr('action');
        if (!$patchIdInput.val()) {
            $patchIdError.html('Patch ID cannot be empty');
            return;
        }
        $form.find('input, button').prop('disabled', true);
        $.post(url, data, null, 'json')
            .done(function (result) {
                if (!result.error) {
                    $resultContainer.html('');
                    render_result(result);
                    $resultContainer.show();
                    $legendContainer.show();
                    $patchIdError.html('');
                } else {
                    $patchIdError.html(result.error);
                }
            })
            .always(function () {
                $form.find('input, button').prop('disabled', false);
            });
    });

    if ($patchIdInput.val()) {
        $form.submit();
    }

    function render_result(result) {
        var release, groupName, groupResults
        for (groupName in result.check_results) {
            groupResults = result.check_results[groupName];

            var output = '<table class="result_table">'
                + '<thead>'
                + '<tr class="header">'
                + '<td colspan="3">' + groupName + '</td>'
                + '</tr>'
                + '<tr class="subheader">'
                + '<td>Version</td>'
                + '<td>Patch</td>'
                + '<td>Git</td>'
                + '</tr>'
                + '</thead>';

            for (release in groupResults) {
                release = groupResults[release];

                output += '<tr><td';
                if (release.check_strategy == 'n/a') {
                    if (release.instance_name == 'n/a') {
                        var columnContent = '&nbsp;';
                    } else {
                        var columnContent = release.instance_name
                    }
                    output += ' colspan="3">' + columnContent;
                } else if (release.check_strategy == 'merged') {
                    output += '>' + release.instance_name + '</td>'
                        + '<td class="td_merged" colspan="2">Merged';
                } else {
                    var falseResultClass = 'td_fail';
                    for (var strategyResult in release.check_strategy) {
                        if (release.check_strategy[strategyResult] == 1) {
                            falseResultClass = 'td_adaptation_required';
                            break;
                        }
                    }

                    output += '>' + release.instance_name + '</td>';
                    if (release.check_strategy['patch'] == 1) {
                        output += '<td class="td_ok">Ok';
                    } else {
                        output += '<td class="' + falseResultClass + '">No';
                    }
                    if (release.check_strategy['git_apply'] == 1) {
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
    }
})
