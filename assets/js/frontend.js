jQuery(document).ready(function($) {

	maybeHideLowestPrice();

	$('form.variations_form').on('found_variation', function(event, variation) {

		const $wrapper = $( '.wc-price-history.prior-price.lowest' ),
		  $lowestPricePlaceholder = $( '.wc-price-history.prior-price-value .wc-price-history-lowest-raw-value'),
		  lowestInVariation = variation._wc_price_history_lowest_price;

		$wrapper.show();

		 if ( $lowestPricePlaceholder.length ) {
			 $lowestPricePlaceholder.text( formatPrice( lowestInVariation ) );
		 }
	});

	$('form.variations_form').on('reset_data', function(event) {

		maybeHideLowestPrice();
	});

	function formatPrice(price) {

		let formattedPrice = parseFloat( price ).toFixed( wc_price_history_frontend.decimals );

		formattedPrice = formattedPrice.replace(',', wc_price_history_frontend.thousand_separator);
		formattedPrice = formattedPrice.replace('.', wc_price_history_frontend.decimal_separator);

		return formattedPrice;
	}

	function maybeHideLowestPrice() {

		$( '.wc-price-history.prior-price.lowest' ).each(function() {

			const $lowestPricePlaceholder = $( this );

			if ( $lowestPricePlaceholder.data( 'product-type' ) !== 'variable' ) {
				return;
			}

			if ( wc_price_history_frontend.variant_before_selection === 'lowest_hide' ) {
				$lowestPricePlaceholder.hide();
			}
		} );
	}
});