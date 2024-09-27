<?php

namespace PriorPrice;

class ProductUpdates {

	/**
	 * @var \PriorPrice\HistoryStorage
	 */
	private $history_storage;

	public function __construct( HistoryStorage $history_storage ) {

		$this->history_storage = $history_storage;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.1
	 */
	public function register_hooks(): void {

		add_action( 'woocommerce_new_product', [ $this, 'start_price_history' ] );
		add_action( 'woocommerce_new_product_variation', [ $this, 'start_price_history' ] );
		add_action( 'woocommerce_update_product', [ $this, 'update_price_history' ] );
		add_action( 'woocommerce_save_product_variation', [ $this, 'update_price_history' ] );
	}

	/**
	 * Update price history.
	 *
	 * @since 1.1
	 *
	 * @param int $product_id Product ID.
	 */
	public function update_price_history( int $product_id ): void {

		remove_action( 'woocommerce_update_product', [ $this, 'update_price_history' ] );

		if ( get_post_status( $product_id ) === 'draft' ) {
			return;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$this->history_storage->add_price( $product_id, (float) $product->get_price(), false );
		$this->maybe_update_price_history_for_variation( $product );
	}

	/**
	 * Start price history.
	 *
	 * @since 1.7.4
	 *
	 * @param int $product_id Product ID.
	 */
	public function start_price_history( int $product_id ): void {

		if ( ProductDuplicate::$is_during_duplication ) {
			return;
		}

		if ( get_post_status( $product_id ) === 'draft' ) {
			return;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$this->history_storage->add_first_price( $product_id, (float) $product->get_price() );
	}

	/**
	 * Update price history for variations.
	 *
	 * @since {VERSION}
	 *
	 * @param WC_Product_Variable|WC_Product $product Product.
	 */
	private function maybe_update_price_history_for_variation( $product ): void {

		if ( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				$product = wc_get_product( $variation['variation_id'] );
				$this->history_storage->add_price( $product->get_id(), (float) $product->get_price(), false );
			}
		}
	}
}
