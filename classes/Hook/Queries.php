<?php

namespace Autocampaigner\Hook;


class Queries extends BaseHooks {


	use \Autocampaigner\Queries;



	public function __construct() {


		// image search for editor
		add_action( 'wp_ajax_autocampaigner_search_image', [ $this, 'autocampaigner_search_image' ] );

		// search posts for editor
		add_action( 'wp_ajax_autocampaigner_search_posts', [ $this, 'autocampaigner_search_posts' ] );
	}



	public function autocampaigner_search_image() {


		$this->verify_nonce();
		wp_die( json_encode( $this->search_images( sanitize_text_field( $_POST['search'] ) ) ) );
	}





	public function autocampaigner_search_posts() {

		$this->verify_nonce();
		$results = $this->search_posts(
			sanitize_text_field( $_POST['search'] ),
			sanitize_text_field( $_POST['type'] ) == '' ? 'post' : sanitize_text_field( $_POST['type'] )
		);
		wp_die( json_encode( $results ) );

	}



}