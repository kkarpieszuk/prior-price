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

		if ( get_post_status( $product_id ) === 'draft' ) {
			return;
		}

		$product_price = get_post_meta( $product_id, '_price', true );

		$this->history_storage->add_price( $product_id, (float) $product_price, true );
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

		$product_price = get_post_meta( $product_id, '_price', true );

		$this->history_storage->add_first_price( $product_id, (float) $product_price );
	}
}
