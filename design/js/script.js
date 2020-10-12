$().ready(function () {
    $('#legend_header_div')
        .mouseenter(function () {
            $('#legend_fold_icon').css('filter', 'brightness(1)');
        })
        .mouseleave(function () {
            $('#legend_fold_icon').css('filter', 'brightness(0.85)');
        })
        .click(function () {
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
        var output, release, groupName, groupResults, falseResultClass, strategyResult
        for (groupName in result.check_results) {
            groupResults = result.check_results[groupName];

            output = '<table class="result_table release_table">'
                + '<thead>'
                + '<tr class="header">'
                + '<td colspan="3">' + groupName + '</td>'
                + '</tr>'
                + '<tr class="subheader">'
                + (result.check_method !== 'mqp'
                        ? '<td rowspan="2">Version</td>'
                        : '<td>Version</td>'
                )
                + (result.check_method !== 'mqp'
                        ? '<td colspan="2">Is Compatible</td>'
                        : '<td class="colspan_2">Is Compatible</td>'
                )
                + '</tr>'
                + (result.check_method !== 'mqp'
                        ? '<tr class="subheader"><td>using Patch</td><td>using Git</td></tr>'
                        : ''
                )
                + '</thead>';

            for (release in groupResults) {
                release = groupResults[release];

                output += '<tr><td';
                if (release.result === 'n/a') {
                    output += ' colspan="3">' + (release.instance_name === 'n/a' ? '&nbsp;' : release.instance_name);
                } else if (release.result === 'merged') {
                    output += '>' + release.instance_name + '</td>'
                        + '<td class="td_merged" colspan="2">Merged';
                } else if (release.result === 1) {
                    output += '>' + release.instance_name + '</td>'
                        + '<td class="td_ok colspan_2">Ok';
                } else if (release.result === 0) {
                    output += '>' + release.instance_name + '</td>'
                        + '<td class="td_fail colspan_2">No';
                } else {
                    falseResultClass = 'td_fail';
                    for (strategyResult in release.result) {
                        if (release.result[strategyResult] === 1) {
                            falseResultClass = 'td_adaptation_required';
                            break;
                        }
                    }

                    output += '>' + release.instance_name + '</td>';
                    if (release.result['patch'] === 1) {
                        output += '<td class="td_ok">Ok';
                    } else {
                        output += '<td class="' + falseResultClass + '">No';
                    }
                    if (release.result['git_apply'] === 1) {
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
