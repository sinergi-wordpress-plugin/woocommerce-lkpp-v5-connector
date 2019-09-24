jQuery(function($){
	// simple multiple select
    // $('#lkpp_product_category_id').select2();
    // $('#lkpp_brand_id').select2();
 
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
});