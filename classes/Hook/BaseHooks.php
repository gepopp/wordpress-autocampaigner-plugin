<?php

namespace Autocampaigner\Hook;

class BaseHooks {





	public $model;





	public function execute() {

		$this->verify_nonce();

		$action = sanitize_text_field( $_POST['action'] );

		$this->{$action}();


	}





	public function verify_nonce( $nonce = false ) {

		$nonce = ! $nonce ? 'wp_rest' : $nonce;

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), $nonce ) ) {
			wp_die( 'hack', 400 );
		}
	}

}