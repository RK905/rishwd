/**
 * Created by apurba on 14/10/17.
 */
(function($) {

    $(document).ready(function () {

        'use strict';

        if (typeof wpcf7 === 'undefined' || typeof  wpcf7.taggen.compose === 'undefined' ||  wpcf7.taggen.compose === null) {
            return;
        }
        wpcf7.taggen.compose = function (tagType, $form) {
            var name = $form.find('input[name="name"]').val();
            var scope = $form.find('.scope.' + tagType);

            if (!scope.length) {
                scope = $form;
            }

            var options = [];

            scope.find('input.option').not(':checkbox,:radio').each(function (i) {
                var val = $(this).val();

                if (!val) {
                    return;
                }

                if ($(this).hasClass('filetype')) {
                    val = val.split(/[,|\s]+/).join('|');
                }

                if ($(this).hasClass('color')) {
                    val = '#' + val;
                }

                if ('class' == $(this).attr('name')) {
                    $.each(val.split(' '), function (i, n) {
                        options.push('class:' + n);
                    });
                } else {
                    options.push($(this).attr('name') + ':' + val);
                }
            });

            scope.find('input:checkbox.option').each(function (i) {
                if ($(this).is(':checked')) {
                    options.push($(this).attr('name'));
                }
            });

            scope.find('input:radio.option').each(function (i) {
                if ($(this).is(':checked') && !$(this).hasClass('default')) {
                    options.push($(this).attr('name') + ':' + $(this).val());
                }
            });

            scope.find('select#filetrip_shortcode_id').each(function (i) {
                var selectVal= $(this).val();
                if(selectVal.length){

                    options.push($(this).attr('id') + ':' + selectVal );
                }


            });

            if ('radio' == tagType) {
                options.push('default:1');
            }

            options = ( options.length > 0 ) ? options.join(' ') : '';

            var value = '';

            if (scope.find(':input[name="values"]').val()) {
                $.each(
                    scope.find(':input[name="values"]').val().split("\n"),
                    function (i, n) {
                        value += ' "' + n.replace(/["]/g, '&quot;') + '"';
                    }
                );
            }

            var components = [];

            $.each([tagType, name, options, value], function (i, v) {
                v = $.trim(v);

                if ('' != v) {
                    components.push(v);
                }
            });

            components = $.trim(components.join(' '));
            return '[' + components + ']';
        };

    })

    $('.wpcf7-submit').on('click',function () {
        alert('hi')

        console.log($('input[name="image-id[]"]'))
    })


})(jQuery)