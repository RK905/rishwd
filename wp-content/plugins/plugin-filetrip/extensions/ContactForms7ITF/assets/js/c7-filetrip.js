/**
 * Created by apurba on 14/10/17.
 */
(function($) {

    $(document).ready(function () {

        'use strict';

        jQuery(document).on('click', '.wpcf7-submit', function(e){
            
            var isRequired =  $('#filetrip-cf').data('required')


            if($('input[name="image-id[]"]').val() !==undefined){

                $('#filetrip-cf').val('.')

            }

            if(isRequired && ''== $('#filetrip-cf').val()){
                $(' <span role="alert" class="wpcf7-not-valid-tip">This is a required field.</span>').insertAfter('#filetrip-cf')
                e.preventDefault();

            }
        });



 

    })
})(jQuery)