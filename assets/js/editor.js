(function ( $ ) {
	'use strict';
	$( document ).ready( function () {
		// Remove keywords.
		$( '#wp-api-keyword-wrapper' ).on( 'click', 'button.wp-api-remove-keyword', function ( e ) {
			$( this ).closest( '.wp-api-keyword-pill' ).remove();
		} );

		// Add a new keyword.
		$( '#wp-api-keyword-field' ).on( 'keypress', function ( e ) {
			// When enter is pressed
			if ( 'Enter' !== e.key ) {
				return;
			}

			e.preventDefault();

			// Remove whitespace
			let value = $( this ).val().replace( /\s/g, '' );

			// Search for duplicates
			if ( 0 === $( '#wp-api-keyword-wrapper span[data-value="' + value + '"]' ).length ) {
				// Add the keyword to the list
				$( '#wp-api-keyword-wrapper' ).prepend( wp_api_keyword_template( value ) );
			}

			// Empty the value
			$( this ).val( '' );

		} );
	} );

	/**
	 * Generate a new pill for the given keyword.
	 *
	 * @param text
	 * @returns {string}
	 */
	function wp_api_keyword_template ( text ) {
		let uuid = randomUuid();

		return '<span class="components-form-token-field__token wp-api-keyword-pill" data-value="' + text + '">' +
			'<span class="components-form-token-field__token-text" id="wp-api-single-keyword-' + uuid + '">' +
			'<span aria-hidden="true">' + text + '</span>' +
			'</span>' +
			'<button ' +
			'type="button" ' +
			'aria-describedby="wp-api-single-keyword-' + uuid + '" ' +
			'class="components-button components-form-token-field__remove-token has-icon wp-api-remove-keyword" ' +
			'aria-label="Remove Keyword">' +
			'<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">' +
			'<path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path>' +
			'</svg>' +
			'</button>' +
			'<input type="hidden" name="wp_api_search_keywords[]" value="' + text + '">' +
			'</span>';
	}

	/**
	 * Generate an id to be used for a div.
	 *
	 * @returns {string}
	 */
	function randomUuid () {
		var string = function () {
			return (((1 + Math.random()) * 0x10000) | 0).toString( 16 ).substring( 1 );
		};
		return (string() + string() + "-" + string() + "-" + string() + "-" + string() + "-" + string() + string() + string());
	}
})( jQuery );