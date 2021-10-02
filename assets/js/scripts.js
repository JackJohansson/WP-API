(function ( $ ) {
	'use strict';
	$( document ).ready( function () {

		// Initialize select2
		$( '.wp-api-select2' ).select2( {
			placeholder: "Select",
			minimumResultsForSearch: Infinity,
			width: '100%',
			dropdownAutoWidth: true
		} );

		// Initialize touchspin
		$( '.wp-api-rate-per' ).TouchSpin( {
			buttondown_class: 'btn btn-secondary',
			buttonup_class: 'btn btn-secondary',
			min: 1,
			max: 60,
			step: 1,
			decimals: 0,
			boostat: 5,
			maxboostedstep: 10,
		} );

		// Generate a new token on demand
		$( '#wp-api-regenerate-token' ).on( 'click', function ( e ) {
			e.preventDefault();
			let button = $( this );
			let url    = button.attr( 'data-url' );
			let nonce  = button.attr( 'data-nonce' );

			$.ajax( {
				url: url,
				method: 'post',
				dataType: 'json',
				data: {
					action: 'wp_api_generate_token',
					wp_api_generate_token_nonce: nonce
				},
				beforeSend: ( xhr ) => {
					button.addClass( 'wp-api-spinner spinner--md spinner--light' ).prop( 'disabled', 'disabled' ).find( 'i' ).removeClass( 'la la-refresh font-light' );
				},
				error: ( xhr, status, error ) => {
					console.log( error );
				},
				success: ( data, textStatus, jqXHR ) => {
					if ( data.status ) {
						location.reload();
					}
				},
				complete: ( jqHXR, textStatus ) => {
					button.removeClass( 'wp-api-spinner spinner--md spinner--light' ).find( 'i' ).addClass( 'la la-refresh font-light' );
				}
			} );
		} );
	} );

})( jQuery );