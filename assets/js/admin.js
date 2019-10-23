jQuery(function($){
	// simple multiple select
    // $('#lkpp_product_category_id').select2();
    // $('#lkpp_brand_id').select2();

    // Set 2 digit month and date
    function getTwoDigitDateFormat(monthOrDate) {
        return (monthOrDate < 10) ? '0' + monthOrDate : '' + monthOrDate;
    }

	// multiple select with AJAX search
	$('#lkpp_product_category_id').select2({
  		ajax: {
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
        				q: params.term, // search query
        				action: 'lkppgetcateg' // AJAX action for admin-ajax.php
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) {
 
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
 
				}
				return {
					results: options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
    });
    
    // multiple select with AJAX search
	$('#lkpp_brand_id').select2({
        ajax: {
              url: ajaxurl, // AJAX URL is predefined in WordPress admin
              dataType: 'json',
              delay: 250, // delay in ms while typing when to perform a AJAX search
              data: function (params) {
                    return {
                      q: params.term, // search query
                      action: 'lkppgetbrand' // AJAX action for admin-ajax.php
                    };
              },
              processResults: function( data ) {
              var options = [];
              if ( data ) {

                  // data is the array of arrays, and each of them contains ID and the Label of the option
                  $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                      options.push( { id: text[0], text: text[1]  } );
                  });

              }
              return {
                  results: options
              };
          },
          cache: true
      },
      minimumInputLength: 3 // the minimum of symbols to input before perform a search
    });
    

    // render datepicker
    $('.lkpp_expired_date').datepicker({
        dateFormat : 'yy-mm-dd'
    });

    // Hide or Show tkdn field
    $("#local_product").change(function() {
        $(".tkdn_field").hide();

        if ($(this).val() == "yes") {
            $(".tkdn_field").show();
        }
    }).change();

    // Calculate LKPP Disc Percentage when LKPP price changed
    $("#lkpp_disc").change(function() {

        $lkpp_disc = $(this).val();
        $web_price = $("#_regular_price").val();
        $lkpp_price_exc = $web_price - ($web_price * ($lkpp_disc/100));
        $lkpp_price_inc = Math.round($lkpp_price_exc + ($lkpp_price_exc * (10/100)));
        document.getElementById('lkpp_price').value = $lkpp_price_inc;
        

    }).change();

    // Calculate LKPP Disc Percentage when Web price changed
    /*$("#_regular_price").change(function() {
        $web_price = $(this).val();
        $lkpp_price_inc = $("#lkpp_price").val();
        $lkpp_price_exc = Math.round($lkpp_price_inc - ($lkpp_price_inc * (10/100)));
        $lkpp_disc = 100 - Math.round(($lkpp_price_exc/$web_price) * 100);
        document.getElementById('lkpp_disc').value = $lkpp_disc;

    }).change();*/

    // Detect value change
    var woo_price_elem = $("#_regular_price");
    var lkpp_disc_elem = $("#lkpp_disc");

    // Woo price change detection
    woo_price_elem.data('oldVal', woo_price_elem.val());
    woo_price_elem.bind('propertychange keyup input paste', function(event){
        if ($(this).data('oldVal') != $(this).val()){
            // Updated stored value
            $(this).data('oldVal', $(this).val());

            $web_price = $(this).val();
            $lkpp_price_inc = $("#lkpp_price").val();
            $lkpp_price_exc = Math.round($lkpp_price_inc - ($lkpp_price_inc * (10/100)));
            $lkpp_disc = 100 - Math.round(($lkpp_price_exc/$web_price) * 100);
            document.getElementById('lkpp_disc').value = $lkpp_disc;

            // Set updated time
            var today = new Date();
            var date = today.getFullYear()+'-'+getTwoDigitDateFormat(today.getMonth() + 1)+'-'+getTwoDigitDateFormat(today.getDate());
            var time = today.getHours() + ":" + getTwoDigitDateFormat(today.getMinutes()) + ":" + getTwoDigitDateFormat(today.getSeconds());
            var dateTime = date+' '+time;
            document.getElementById('price_update').value = 'updated';
        }
    });

    // Lkpp price change detection
    lkpp_disc_elem.data('oldVal', lkpp_disc_elem.val());
    lkpp_disc_elem.bind('propertychange keyup input paste', function(event){
        if ($(this).data('oldVal') != $(this).val()){
            // Updated stored value
            $(this).data('oldVal', $(this).val());

            // Set updated time
            var today = new Date();
            var date = today.getFullYear()+'-'+getTwoDigitDateFormat(today.getMonth() + 1)+'-'+getTwoDigitDateFormat(today.getDate());
            var time = today.getHours() + ":" + getTwoDigitDateFormat(today.getMinutes()) + ":" + getTwoDigitDateFormat(today.getSeconds());
            var dateTime = date+' '+time;
            document.getElementById('price_update').value = 'updated';
        }
    });

});