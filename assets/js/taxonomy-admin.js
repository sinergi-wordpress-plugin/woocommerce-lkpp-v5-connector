(function($) {
    $(function() {
        
        $('.lcateg-sync').on('click', function(){
            $(this).text('Synchronizing...').attr('disabled', 'disabled').removeClass('button-primary');
            $.post(ajaxurl, {
                'action': 'lcateg_sync'
            }, function(data) {
                if(data.message == 'success'){
                    window.location.reload(true);
                } else if(data.message == 'failed'){
                    $('.lcateg-sync').text('Synchronize Category').removeAttr('disabled').addClass('button-primary');
                    alert('Failed to sync product category');
                } else if(data.message == 'secret_failed'){
                    $('.lcateg-sync').text('Synchronize Category').removeAttr('disabled').addClass('button-primary');
                    alert('Please input secret access');
                } else if(data.message == 'url_failed'){
                    $('.lcateg-sync').text('Synchronize Category').removeAttr('disabled').addClass('button-primary');
                    alert('Please input request URL');
                }
            }, 'json');
        });

    });

    $(function() {
        $('.lbrand-sync').on('click', function(){
            $(this).text('Synchronizing...').attr('disabled', 'disabled').removeClass('button-primary');
            $.post(ajaxurl, {
                'action': 'lbrand_sync'
            }, function(data) {
                if(data.message == 'success'){
                    window.location.reload(true);
                } else if(data.message == 'failed'){
                    $('.lbrand-sync').text('Synchronize Brand').removeAttr('disabled').addClass('button-primary');
                    alert('Failed to sync product brand');
                } else if(data.message == 'secret_failed'){
                    $('.lbrand-sync').text('Synchronize Brand').removeAttr('disabled').addClass('button-primary');
                    alert('Please input secret access');
                } else if(data.message == 'url_failed'){
                    $('.lbrand-sync').text('Synchronize Brand').removeAttr('disabled').addClass('button-primary');
                    alert('Please input request URL');
                }
            }, 'json');
        });
    });

})(jQuery);