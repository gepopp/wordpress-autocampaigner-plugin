<?php

namespace Autocampaigner;

class Enqueue {


	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

	}

	public function enqueue_admin_scripts() {

		$ext = '.min';
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$ext = '';
		}

		wp_register_script( 'autocampaigner_admin_script', AUTOCAMPAIGNER_URL . "dist/campaigner_admin{$ext}.js", [
			'jquery',
			'wp-color-picker',
		], AUTOCAMPAIGNER_VERSION, true );
		wp_localize_script( 'autocampaigner_admin_script', 'xhr', [
			'rootapiurl' => esc_url_raw( rest_url() ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
		] );
		wp_enqueue_script( 'autocampaigner_admin_script' );

		wp_enqueue_style(
			'autocampaigner_styles',
			trailingslashit( AUTOCAMPAIGNER_URL ) . "dist/campaigner_admin.css",
			[],
			AUTOCAMPAIGNER_VERSION
		);

	}

}