<?php
namespace Autocampaigner\Hook;

class BaseHooks {





	public function verify_nonce( $nonce = false ) {

		$nonce = ! $nonce ? 'wp_rest' : $nonce;

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), $nonce ) ) {
			wp_die( 'hack', 400 );
		}
	}

}